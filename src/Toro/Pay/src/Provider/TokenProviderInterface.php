<?php

namespace Toro\Pay\Provider;

use League\OAuth2\Client\Token\AccessToken;

interface TokenProviderInterface
{
    /**
     * @return AccessToken
     */
    public function getToken(): AccessToken;

    /**
     * @param AccessToken $token
     */
    public function storeToken(AccessToken $token): void;
}
