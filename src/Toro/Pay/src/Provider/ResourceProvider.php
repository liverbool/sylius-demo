<?php

declare(strict_types=1);

namespace Toro\Pay\Provider;

use GuzzleHttp\Exception\ClientException;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Toro\Pay\ToroPay;

class ResourceProvider extends GenericProvider implements ResourceProviderInterface
{
    /**
     * @var string
     */
    protected $urlResource;

    /**
     * @var string
     */
    protected $apiVersion;

    /**
     * @var OwnerProviderInterface
     */
    protected $ownerProvider;

    /**
     * @var string
     */
    protected $userAgent;

    public function __construct(array $options = [], array $collaborators = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $options = $resolver->resolve($options);

        parent::__construct($options, $collaborators);
    }

    /**
     * @param OptionsResolver $resolver
     */
    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['clientId', 'clientSecret', 'ownerProvider', 'redirectUri']);

        $resolver->setDefault('sandbox', true);
        $resolver->setDefault('apiVersion', 'v1');
        $resolver->setDefault('userAgent', sprintf('%s/%s', ToroPay::SERVICE_NAME, ToroPay::VERSION));
        $resolver->setDefault('urlAuthorize', function (Options $options) {
            return $this->getBaseUrl($options) . '/oauth/authorize';
        });
        $resolver->setDefault('urlAccessToken', function (Options $options) {
            return $this->getBaseUrl($options) . '/oauth/token';
        });
        $resolver->setDefault('urlResourceOwnerDetails', function (Options $options) {
            return $this->getBaseUrl($options) . sprintf('/api/%s/user/info', $options['apiVersion']);
        });
        $resolver->setDefault('urlResource', function (Options $options) {
            return $this->getBaseUrl($options) . sprintf('/api/%s', $options['apiVersion']);
        });

        $resolver->setNormalizer('clientId', function (OptionsResolver $resolver, $value) {
            return (string)$value;
        });

        $resolver->setAllowedTypes('sandbox', 'boolean');
        $resolver->setAllowedTypes('clientId', 'string');
        $resolver->setAllowedTypes('clientSecret', 'string');
        $resolver->setAllowedTypes('apiVersion', 'string');
        $resolver->setAllowedTypes('redirectUri', 'string');
        $resolver->setAllowedTypes('urlAuthorize', 'string');
        $resolver->setAllowedTypes('urlAccessToken', 'string');
        $resolver->setAllowedTypes('urlResourceOwnerDetails', 'string');
        $resolver->setAllowedTypes('urlResource', 'string');
        $resolver->setAllowedTypes('userAgent', 'string');
        $resolver->setAllowedTypes('ownerProvider', OwnerProviderInterface::class);

        $resolver->setAllowedValues('redirectUri', function ($value) {
            return filter_var($value, FILTER_VALIDATE_URL);
        });
        $resolver->setAllowedValues('urlResource', function ($value) {
            return filter_var($value, FILTER_VALIDATE_URL);
        });
        $resolver->setAllowedValues('urlResourceOwnerDetails', function ($value) {
            return filter_var($value, FILTER_VALIDATE_URL);
        });
        $resolver->setAllowedValues('urlAuthorize', function ($value) {
            return filter_var($value, FILTER_VALIDATE_URL);
        });
        $resolver->setAllowedValues('urlAccessToken', function ($value) {
            return filter_var($value, FILTER_VALIDATE_URL);
        });
    }

    /**
     * @param Options $options
     *
     * @return string
     */
    private function getBaseUrl(Options $options)
    {
        return $options['sandbox'] ? ToroPay::BASE_URL_SANDBOX : ToroPay::BASE_URL;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessToken($grant, array $options = [])
    {
        $token = parent::getAccessToken($grant, $options);

        // store token
        $this->ownerProvider->store($token, $this->getResourceOwner($token));

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenUsingAuthorizationCode(string $code): AccessToken
    {
        return $this->getAccessToken('authorization_code', ['code' => $code]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenUsingRefreshToken(string $refreshToken): AccessToken
    {
        return $this->getAccessToken('refresh_token', ['refresh_token' => $refreshToken]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenUsingClientCredentials(): AccessToken
    {
        return $this->getAccessToken('client_credentials');
    }

    /**
     * {@inheritdoc}
     */
    public function refreshToken(): void
    {
        // will send empty token to server, and accept 401
        // FIXME: find the better throw error, for dev? or for user?
        $refreshToken = '';

        if ($token = $this->ownerProvider->getToken()) {
            $refreshToken = $token->getRefreshToken();
        }

        $this->getAccessTokenUsingRefreshToken($refreshToken);
    }

    /**
     * {@inheritdoc}
     */
    public function getResource($method, $path, array $data = [], array $headers = []): array
    {
        if ('GET' !== strtoupper($method)) {
            $headers = array_replace_recursive(['Content-Type' => 'application/json; charset=utf-8'], $headers);
        }

        // clean resource path
        // 1. remove full path
        $path = str_replace($this->urlResource, '', $path);
        // 2. remove ^/api path
        $path = str_replace('/api/' . $this->apiVersion, '', $path);

        // remove double slash
        $uri = preg_replace('/([^:])(\/{2,})/', '$1/', $this->urlResource . $path);

        // will send empty token to server, and accept 401
        // FIXME: find the better throw error, for dev? or for user?
        $accessToken = null;
        $refreshToken = null;

        if ($token = $this->ownerProvider->getToken()) {
            $accessToken = $token->getToken();
            $refreshToken = $token->getRefreshToken();
        }

        try {
            $response = $this->getResponse(
                $this->getAuthenticatedRequest($method, $uri, $accessToken, [
                    'body' => !empty($data) ? json_encode($data) : null,
                    'headers' => array_replace_recursive([
                        'User-Agent' => $this->userAgent,
                    ], $headers),
                ])
            );
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }

        try {
            $contentBody = $this->parseJson((string)$response->getBody());
        } catch (\UnexpectedValueException $e) {
            $contentBody = [];
        }

        // 400 - bad request, invalid request parameter
        // 401 - access denied, invalid grant type or invalid token
        // 403 - access denied, invalid scope
        // 404 - not found
        // 405 - request method not allow
        if (401 === $response->getStatusCode() && $refreshToken && 'invalid_grant' === @$contentBody['error']) {
            $this->refreshToken();

            return $this->getResource($method, $path, $data, $headers);
        }

        if ($response->getStatusCode() >= 400) {
            $contentBody[self::RESOURCE_NAME_KEY] = 'error';
        }

        // handle pagin api
        if (array_key_exists('_embedded', $contentBody)) {
            $contentBody[self::RESOURCE_NAME_KEY] = 'paginage';
        }

        return $contentBody;
    }

    /**
     * {@inheritdoc}
     */
    public function getStoredAccessToken(): ?AccessToken
    {
        return $this->ownerProvider->getToken();
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceOwner(AccessToken $token): ResourceOwnerInterface
    {
        $owner = parent::getResourceOwner($token);

        $this->ownerProvider->store($token, $owner);

        return $owner;
    }

    /**
     * {@inheritdoc}
     */
    public function authorizeWebAction(?string $code = null, ?string $state = null): int
    {
        $sessionKey = 'toro_pay_oauth2_state';

        if (empty($code)) {
            $_SESSION[$sessionKey] = $this->getState();

            header('Location: ' . $this->getAuthorizationUrl());
            return 1;
        }

        if (empty($state) || (isset($_SESSION[$sessionKey]) && $state !== $_SESSION[$sessionKey])) {
            if (isset($_SESSION[$sessionKey])) {
                unset($_SESSION[$sessionKey]);
            }

            return 0;
        }

        // get access token & store resource owner
        $this->getAccessTokenUsingAuthorizationCode($code);

        return 2;
    }
}
