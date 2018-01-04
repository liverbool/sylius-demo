<?php

namespace Tests\Toro\Pay\Provider;

use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Tests\Toro\Pay\MockerTrait;
use Toro\Pay\Bridge\Sylius\OwnerProvider;
use Toro\Pay\Provider\ResourceProvider;
use Toro\Pay\Provider\ResourceProviderInterface;

class ResourceProviderTest extends TestCase
{
    use MockerTrait;

    public function testValidRequirements()
    {
        $provider = $this->createValidResourceProvider();

        self::assertInstanceOf(ResourceProviderInterface::class, $provider);
    }

    public function testInvalidRequirements()
    {
        $this->expectException(InvalidOptionsException::class);

        new ResourceProvider([
            'clientId' => 123,
            'clientSecret' => 456,
            'redirectUri' => 'invalid-your-uri',
            'ownerProvider' => $this->createOwnerProvider(),
        ]);
    }

    public function testMissingRequirements()
    {
        $this->expectException(MissingOptionsException::class);

        new ResourceProvider([]);
    }

    public function XtestAuthorizeWebAction()
    {
        $testAccessToken = 'testAccessToken';
        $ownerProvider = new OwnerProvider($this->createTokenStorage($this->createSyliusUserWithOAuth()));
        $provider = $this->createValidResourceProvider($ownerProvider, json_encode([
            'access_token' => $testAccessToken,
        ]));

        // 1. no code
        $result = $provider->authorizeWebAction();
        self::assertEquals(1, $result);

        // 2. have code but have no state
        $result = $provider->authorizeWebAction('abc');
        self::assertEquals(0, $result);

        // 3. invalid state
        $_SESSION['toro_pay_oauth2_state'] = 'xxx';
        $result = $provider->authorizeWebAction('abc', 'invalid_state');
        self::assertEquals(0, $result);

        // 4. valid state
        $_SESSION['toro_pay_oauth2_state'] = 'valid_state';
        $result = $provider->authorizeWebAction('abc', 'valid_state');

        self::assertEquals(2, $result);
        self::assertEquals($testAccessToken, $ownerProvider->getToken()->getToken());
    }
}
