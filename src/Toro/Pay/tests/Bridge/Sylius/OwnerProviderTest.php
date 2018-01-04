<?php

namespace Tests\Toro\Pay\Bridge\Sylius;

use League\OAuth2\Client\Token\AccessToken;
use PHPUnit\Framework\TestCase;
use Tests\Toro\Pay\MockerTrait;
use Toro\Pay\Bridge\Sylius\OwnerProvider;

class OwnerProviderTest extends TestCase
{
    use MockerTrait;

    public function testInvalidUserType()
    {
        $tokenStorage = $this->createTokenStorage($this->createSymfonyUser());

        $provider = new OwnerProvider($tokenStorage);

        self::assertEquals(null, $provider->getToken());
    }

    public function testValidUserTypeButHaveNoAccessToken()
    {
        $tokenStorage = $this->createTokenStorage($this->createSyliusUser());

        $provider = new OwnerProvider($tokenStorage);

        self::assertEquals(null, $provider->getToken());
    }

    public function testValidUserTypeAndAccessToken()
    {
        $tokenStorage = $this->createTokenStorage($this->createSyliusUserWithOAuth());

        $provider = new OwnerProvider($tokenStorage);

        self::assertInstanceOf(AccessToken::class, $provider->getToken());
        self::assertEquals('1', $provider->getToken()->getResourceOwnerId());
    }
}
