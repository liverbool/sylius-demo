<?php

namespace Toro\Pay\Provider;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface ResourceProviderInterface
{
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
