<?php

namespace Tests\Toro\Pay\Provider;

use League\OAuth2\Client\Token\AccessToken;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Tests\Toro\Pay\HttpResponse;
use Tests\Toro\Pay\MockerTrait;
use Tests\Toro\Pay\HttpClientOffline;
use Toro\Pay\Provider\ResourceProvider;
use Toro\Pay\Provider\ResourceProviderInterface;

class ResourceProviderTest extends TestCase
{
    use MockerTrait;

    public function testValidRequirements()
    {
        self::assertInstanceOf(ResourceProviderInterface::class, $this->createResourceProvider());
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

    public function testGetResourceOwner()
    {
        HttpClientOffline::fixture('/user/info', function (HttpResponse $res) {
            return $res->withJson('user_info.json');
        });

        $owner = $this->createResourceProvider()
            ->getResourceOwner($this->createAccessToken($this->sampleValidToken));

        self::assertTrue(!empty($owner->getId()));
    }

    public function testAuthorizeWebAction()
    {
        if ($this->useLiveApi) {
            self::assertTrue(true);

            return;
        }

        $testAccessToken = $this->sampleValidToken;

        HttpClientOffline::fixture('/user/info', function (HttpResponse $res) use ($testAccessToken) {
            return $res->withJson('user_info.json');
        });

        HttpClientOffline::fixture('/oauth/token', function (HttpResponse $res) use ($testAccessToken) {
            return $res->withData(['access_token' => $testAccessToken]);
        });

        $provider = $this->createResourceProvider(['access_token' => $testAccessToken]);

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
        self::assertInstanceOf(AccessToken::class, $accessToken = $provider->getStoredAccessToken());

        self::assertEquals($testAccessToken, $accessToken->getToken());
    }
}
