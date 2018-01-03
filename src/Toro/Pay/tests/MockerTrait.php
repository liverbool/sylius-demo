<?php

declare(strict_types=1);

namespace Tests\Toro\Pay;

use PHPUnit\Framework\TestCase;
use Sylius\Component\User\Model\UserInterface as SyliusUserInterface;
use Sylius\Component\User\Model\UserOAuth;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Toro\Pay\Client\GuzzleHttpClient;
use Toro\Pay\Client\HttpClientInterface;
use Toro\Pay\Provider\TokenProviderInterface;

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

    protected function createSyliusUserWithOAuth()
    {
        $oauth = new UserOAuth();
        $oauth->setAccessToken('test_access_token');
        $oauth->setRefreshToken('test_refresh_token');

        $mock = $this->getMockBuilder(SyliusUserInterface::class)->getMock();
        $mock
            ->expects($this->any())
            ->method('getOAuthAccount')
            ->will($this->returnValue($oauth));

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
     * @return \PHPUnit\Framework\MockObject\MockObject|HttpClientInterface
     */
    protected function createHttpClient()
    {
        if ($this->useLiveApi) {
            $mock = new GuzzleHttpClient();
        } else {
            $mock = $this->getMockBuilder(HttpClientInterface::class)
                ->getMock()
            ;
        }

        return $mock;
    }

    /**
     * @param null|string $accessToken
     * @param null|string $refreshToken
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|TokenProviderInterface
     */
    protected function createTokenProvider(?string $accessToken = null, ?string $refreshToken = null)
    {
        if ($this->useLiveApi && !$accessToken) {
            $accessToken = 'SampleToken';
        }

        $mock = $this->getMockBuilder(TokenProviderInterface::class)->getMock();

        $mock
            ->expects($this->any())
            ->method('getAccessToken')
            ->will($this->returnValue($accessToken));

//        $mock
//            ->expects($this->any())
//            ->method('getRefreshToken')
//            ->will($this->returnValue($refreshToken));

        return $mock;
    }
}
