<?php

namespace Toro\Pay\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;

interface OwnerProviderInterface
{
    /**
     * @return AccessToken
     */
    public function getToken(): AccessToken;

    /**
     * @param AccessToken $token
     * @param ResourceOwnerInterface $owner
     */
    public function store(AccessToken $token, ResourceOwnerInterface $owner): void;
}
