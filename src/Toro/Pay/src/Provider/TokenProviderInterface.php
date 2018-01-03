<?php

namespace Toro\Pay\Provider;

interface TokenProviderInterface
{
    /**
     * @return string
     */
    public function getAccessToken(): ?string;

    /**
     * Store the access token
     *
     * @param null|string $token
     */
    public function setAccessToken(?string $token = null): void;

    /**
     * @return string
     */
    public function getRefreshToken(): ?string;

    /**
     * Store the refresh token
     *
     * @param null|string $token
     */
    public function setRefreshToken(?string $token = null): void;
}
