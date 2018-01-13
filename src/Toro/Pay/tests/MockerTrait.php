<?php

declare(strict_types=1);

namespace Tests\Toro\Pay;

use League\OAuth2\Client\Token\AccessToken;
use PHPUnit\Framework\TestCase;
use Sylius\Component\User\Model\UserInterface as SyliusUserInterface;
use Sylius\Component\User\Model\UserOAuth;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Toro\Pay\Provider\OwnerProviderInterface;
use Toro\Pay\Provider\ResourceProvider;
use Toro\Pay\Provider\ResourceProviderInterface;

/**
 * @mixin TestCase
 */
trait MockerTrait
{
    protected $sampleValidToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImQ4YjA3N2M2Nzg0MjQ3ODUxMmEwMjFlMTg0MDVmZGZkZGY5MjI1MzI1NjQwYjI1MmExMzExNzg1N2U0MDQzNDFiOGM1M2M1NTVlNTdhODdhIn0.eyJhdWQiOiJkZW1vX2NsaWVudCIsImp0aSI6ImQ4YjA3N2M2Nzg0MjQ3ODUxMmEwMjFlMTg0MDVmZGZkZGY5MjI1MzI1NjQwYjI1MmExMzExNzg1N2U0MDQzNDFiOGM1M2M1NTVlNTdhODdhIiwiaWF0IjoxNTE1ODI0NzMxLCJuYmYiOjE1MTU4MjQ3MzEsImV4cCI6MTUxNTgyODMzMSwic3ViIjoiMSIsInNjb3BlcyI6eyJwcm9maWxlIjoicHJvZmlsZSIsImVtYWlsIjoiZW1haWwiLCJjb2luX2luZm8iOiJjb2luX2luZm8ifX0.kLXhDUptYrRhospvIJn1t_RiSWBsSUo-851X5yIz9Or9ROu2NGs9v_N380d3Rp_zxYr1TTQdvtE_QJDSumr1bq5Yqt9WkrMO5HFwod5b5Em0ZK0Iu8m-axhQJIQMLtzhkXrsci2myEGTzl5i2xcFGC4chxr-LxvvqmqLh2uVZaor0d29DEb9fRJInNHblCkUhnNldqXEKxFp1hXFeTnN-w4Cc1iEzrVLe5dnrzfgBFQuQiO9wquPOTxhgM-ePyzm6U8LsYWJUJ6kywlMPZyyslFsCCa0Jm8I0Mhoo-pZmbCWUMXNCjcV45DHXW0z3t4vGcQVHmZoMadDKM7H1FSVcs4Kg7eCtQpJ9Kxf4qh90Jvhmu5N05AmBATRR0aPTNrLyRgfN5TmtsuMLtPNf_7yc8gLnzrSpOjlKnw90fNFwypvdBqYy2WpDpaka2qqiW5rY2-Jluwg1sfrd-nukMn-__WidaaTAFmXwVrKA3nSYcSYi3hDrnEBgle7mpllE6yuaU65jYp0gIOQ8tF5vsQe1TZSS7RiyjmluyoHDYIAi8scRpwmN8tgp05en7HUts8xkk_8Fu0D4eCHgDiiKrKtAMHeU3vHWgBTlu-zMOKr_rHcyDeNG6D9Yr6wO9LFFH9nglI512D2UVJbMnfHgEAeS7HE1jZh2IpExXRPGp4zc5s';
    protected $sampleValidToken404 = '';

    protected $useLiveApi = true;
    protected function createSymfonyUser()
    {
        return $this->getMockBuilder(SymfonyUserInterface::class)->getMock();
    }

    protected function createSyliusUser()
    {
        return $this->getMockBuilder(SyliusUserInterface::class)->getMock();
    }

    protected function createSyliusUserWithOAuth(?string $accessToken = null, ?string $refreshToken = null)
    {
        $oauth = new UserOAuth();
        $oauth->setAccessToken($accessToken ?? 'test_access_token');
        $oauth->setRefreshToken($refreshToken ?? 'test_refresh_token');

        $mock = $this->getMockBuilder(SyliusUserInterface::class)->getMock();
        $mock
            ->expects($this->any())
            ->method('getOAuthAccount')
            ->will($this->returnValue($oauth));

        $mock
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));

        $oauth->setUser($mock);

        return $mock;
    }

    protected function createAuthenticationToken($user = null)
    {
        $mock = $this->getMockBuilder(TokenInterface::class)->getMock();
        $mock
            ->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($user));

        return $mock;
    }

    /**
     * @param null $user
     * @return \PHPUnit\Framework\MockObject\MockObject|TokenStorageInterface
     */
    protected function createTokenStorage($user = null)
    {
        $token = $this->createAuthenticationToken($user);

        $mock = $this->getMockBuilder(TokenStorageInterface::class)->getMock();
        $mock
            ->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($token));

        return $mock;
    }

    /**
     * @param string|null $token
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|OwnerProviderInterface
     */
    protected function createOwnerProvider(?string $token = null)
    {
        $token = new AccessToken([
            'access_token' => $token ?? $this->sampleValidToken,
        ]);

        $mock = $this->getMockBuilder(OwnerProviderInterface::class)->getMock();

        $mock
            ->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($token));

        return $mock;
    }

    /**
     * @param string $accessToken
     *
     * @return AccessToken
     */
    public function createAccessToken(string $accessToken)
    {
        return new AccessToken([
            'access_token' => $accessToken,
        ]);
    }

    /**
     * @param array $options
     *
     * @return ResourceProviderInterface
     */
    public function createResourceProvider(array $options = [])
    {
        HttpClientOffline::$liveMode = $this->useLiveApi;

        return new ResourceProvider([
            'sandbox' => true,
            'clientId' => 'demo_client',
            'clientSecret' => 'secret_demo_client',
            'redirectUri' => 'http://your-uri',
            'ownerProvider' => $this->createOwnerProvider($options['access_token'] ?? null),
        ], [
            'httpClient' => new HttpClientOffline()
        ]);
    }

    /**
     * @return ResourceProviderInterface
     */
    public function createValidTokenResourceProvider()
    {
        return $this->createResourceProvider([
            'access_token' => $this->sampleValidToken
        ]);
    }

    /**
     * @return ResourceProviderInterface
     */
    public function create404TokenResourceProvider()
    {
        return $this->createResourceProvider([
            'access_token' => $this->sampleValidToken404
        ]);
    }
}
