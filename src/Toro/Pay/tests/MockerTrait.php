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
    protected $useLiveApi = false;

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
            'access_token' => $token ?? 'SampleToken',
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
            'access_token' => 'ScopedSampleToken'
        ]);
    }

    /**
     * @return ResourceProviderInterface
     */
    public function create404TokenResourceProvider()
    {
        return $this->createResourceProvider([
            'access_token' => 'ScopedSampleToken404'
        ]);
    }
}
