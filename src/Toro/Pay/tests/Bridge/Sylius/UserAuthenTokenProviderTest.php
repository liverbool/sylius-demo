<?php

declare(strict_types=1);

namespace Tests\Toro\Pay\Bridge\Sylius;

use PHPUnit\Framework\TestCase;
use Tests\Toro\Pay\MockerTrait;
use Toro\Pay\Bridge\Sylius\UserAuthenTokenProvider;

class UserAuthenTokenProviderTest extends TestCase
{
    use MockerTrait;

    public function testInvalidUserType()
    {
        $tokenStorage = $this->createTokenStorage($this->createSymfonyUser());
        $resolver = new UserAuthenTokenProvider($tokenStorage);

        $this->assertEquals(null, $resolver->getAccessToken());
        $this->assertEquals(null, $resolver->getRefreshToken());
    }

    public function testNullUserType()
    {
        $tokenStorage = $this->createTokenStorage(null);
        $resolver = new UserAuthenTokenProvider($tokenStorage);

        $this->assertEquals(null, $resolver->getAccessToken());
        $this->assertEquals(null, $resolver->getRefreshToken());
    }

    public function testValidUserTypeAndHaveToken()
    {
        $tokenStorage = $this->createTokenStorage($this->createSyliusUserWithOAuth());
        $resolver = new UserAuthenTokenProvider($tokenStorage);

        $this->assertEquals('test_access_token', $resolver->getAccessToken());
        $this->assertEquals('test_refresh_token', $resolver->getRefreshToken());
    }
}
