<?php

namespace Toro\Pay\Provider;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface OAuth2ClientProviderInterface
{
    /**
     * Builds the authorization URL.
     *
     * @param  array $options
     * @return string Authorization URL
     */
    public function getAuthorizationUrl(array $options = []);

    /**
     * Redirects the client for authorization.
     *
     * @param  array $options
     * @param  callable|null $redirectHandler
     * @return mixed
     */
    public function authorize(array $options = [], callable $redirectHandler = null);

    /**
     * Requests an access token using a specified grant and option set.
     *
     * @param  mixed $grant
     * @param  array $options
     * @return AccessToken
     */
    public function getAccessToken($grant, array $options = []);

    /**
     * Sends a request instance and returns a response instance.
     *
     * WARNING: This method does not attempt to catch exceptions caused by HTTP
     * errors! It is recommended to wrap this method in a try/catch block.
     *
     * @param  RequestInterface $request
     * @return ResponseInterface
     */
    public function getResponse(RequestInterface $request);

    /**
     * Requests and returns the resource owner of given access token.
     *
     * @param  AccessToken $token
     * @return ResourceOwnerInterface
     */
    public function getResourceOwner(AccessToken $token);

    /**
     * @param string $code
     *
     * @return AccessToken
     */
    public function getAccessTokenUsingAuthorizationCode(string $code): AccessToken;

    /**
     * @param string $refreshToken
     *
     * @return AccessToken
     *
     * @throws IdentityProviderException
     */
    public function getAccessTokenUsingRefreshToken(string $refreshToken): AccessToken;

    /**
     * @return AccessToken
     *
     * @throws IdentityProviderException
     */
    public function getAccessTokenUsingClientCredentials(): AccessToken;

    /**
     * @throws IdentityProviderException
     */
    public function refreshToken(): void;

    /**
     * @param string $method
     * @param string $path
     * @param array $data
     * @param array $headers
     *
     * @return array
     * @throws IdentityProviderException
     */
    public function getResource($method, $path, array $data = [], array $headers = []): array;
}
