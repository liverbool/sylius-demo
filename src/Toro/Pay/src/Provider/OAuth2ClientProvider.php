<?php

namespace Toro\Pay\Provider;

use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Toro\Pay\ToroPay;

class OAuth2ClientProvider extends GenericProvider implements OAuth2ClientProviderInterface
{
    /**
     * @var bool
     */
    private $sandbox = true;

    /**
     * @var string
     */
    private $localeCode = 'th';

    /**
     * @var string
     */
    private $apiVersion = 'v1';

    /**
     * @var TokenProviderInterface
     */
    private $tokenProvider;

    /**
     * @param OptionsResolver $resolver
     */
    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['client_secret', 'client_id', 'token_provider']);

        $resolver->setDefault('sandbox', $this->sandbox);
        $resolver->setDefault('locale_code', $this->localeCode);
        $resolver->setDefault('api_version', $this->apiVersion);

        $resolver->setAllowedTypes('sandbox', 'boolean');
        $resolver->setAllowedTypes('client_secret', 'string');
        $resolver->setAllowedTypes('client_id', 'string');
        $resolver->setAllowedTypes('token_provider', TokenProviderInterface::class);
        $resolver->setAllowedTypes('api_version', 'string');
        $resolver->setAllowedTypes('locale_code', 'string');
    }

    /**
     * {@inheritdoc}
     */
    private function getResourceEndpoint(): string
    {
        return ($this->sandbox ? ToroPay::ENDPOINT_SANDBOX : ToroPay::ENDPOINT) . '/' . $this->apiVersion . '/';
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenUsingAuthorizationCode(string $code): AccessToken
    {
        return parent::getAccessToken('authorization_code', ['code' => $code]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenUsingRefreshToken(string $refreshToken): AccessToken
    {
        return parent::getAccessToken('refresh_token', ['refresh_token' => $refreshToken]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenUsingClientCredentials(): AccessToken
    {
        return parent::getAccessToken('client_credentials');
    }

    /**
     * {@inheritdoc}
     */
    public function refreshToken(): void
    {
        $token = $this->getAccessTokenUsingRefreshToken($this->tokenProvider->getRefreshToken());
        // store token
        $this->tokenProvider->setAccessToken($token->getToken());
        $this->tokenProvider->setRefreshToken($token->getRefreshToken());
    }

    /**
     * {@inheritdoc}
     */
    public function getResource($method, $path, array $data = [], array $headers = []): array
    {
        if ('GET' !== strtoupper($method)) {
            $headers = array_replace_recursive(['Content-Type' => 'application/json; charset=utf-8'], $headers);
        }

        // remove double slash
        $uri = preg_replace('/([^:])(\/{2,})/', '$1/', $this->getResourceEndpoint() . $path);

        $response = $this->getResponse(
            $this->getAuthenticatedRequest($method, $uri, $this->tokenProvider->getAccessToken(), [
                'body' => !empty($data) ? json_encode($data) : null,
                'headers' => $headers,
            ])
        );

        $contentBody = $this->parseJson($response);

        if (401 === $response->getStatusCode() && isset($contentBody['error'])) {
            if ('invalid_grant' === $contentBody['error']) {
                $this->refreshToken();

                return $this->getResource($method, $path, $data, $headers);
            }
        }

        return $contentBody;
    }
}
