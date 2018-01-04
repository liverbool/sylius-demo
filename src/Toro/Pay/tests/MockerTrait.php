<?php

declare(strict_types=1);

namespace Tests\Toro\Pay;

use GuzzleHttp\ClientInterface;
use League\OAuth2\Client\Token\AccessToken;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Sylius\Component\User\Model\UserInterface as SyliusUserInterface;
use Sylius\Component\User\Model\UserOAuth;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Toro\Pay\Bridge\Sylius\OwnerProvider;
use Toro\Pay\Provider\OwnerProviderInterface;
use Toro\Pay\Provider\ResourceProvider;

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
            ->will($this->returnValue($oauth))
        ;

        $mock
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1))
        ;

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
     * @param AccessToken|null $token
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|OwnerProviderInterface
     */
    protected function createOwnerProvider(?AccessToken $token = null)
    {
        if ($this->useLiveApi && !$token) {
            $token = new AccessToken([
                'access_token' => 'SampleToken',
            ]);
        }

        $mock = $this->getMockBuilder(OwnerProviderInterface::class)->getMock();

        $mock
            ->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($token));

        return $mock;
    }

    /**
     * @param string $body
     * @param int $statusCode
     * @param string $reason
     * @return \PHPUnit_Framework_MockObject_MockObject|ResponseInterface
     */
    public function createHttpResponse(string $body, int $statusCode = 200, string $reason = 'ok')
    {
        $mock = $this->getMockBuilder(ResponseInterface::class)->getMock();

        $mock
            ->expects($this->any())
            ->method('getReasonPhrase')
            ->will($this->returnValue($reason))
        ;

        $mock
            ->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue($statusCode))
        ;

        $mock
            ->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($body))
        ;

        return $mock;
    }

    /**
     * @param null|ResponseInterface $response
     * @return \PHPUnit_Framework_MockObject_MockObject|ClientInterface
     */
    public function createHttpClient(?ResponseInterface $response)
    {
        $mock = $this->getMockBuilder(ClientInterface::class)->getMock();

        $mock
            ->expects($this->any())
            ->method('send')
            ->will($this->returnValue($response))
        ;

        return $mock;
    }

    public function createAccessToken(?string $accessToken = null)
    {
        return new AccessToken([
            'access_token' => $accessToken,
        ]);
    }

    public function createValidResourceProvider(?OwnerProviderInterface $ownerProvider = null, string $responseBody = '')
    {
        return new ResourceProvider([
            'sandbox' => true,
            'clientId' => 'demo_client',
            'clientSecret' => 'secret_demo_client',
            'redirectUri' => 'http://your-uri',
            'ownerProvider' => $ownerProvider ?? $this->createOwnerProvider(),
        ], [
            'httpClient' => $this->createHttpClient(
                $this->createHttpResponse($responseBody)
            )
        ]);
    }

    public function createLiveValidResourceProvider(?string $accessToken = null, ?string $refreshToken = null)
    {
        $ownerProvider = new OwnerProvider($this->createTokenStorage($this->createSyliusUserWithOAuth(
            $accessToken ?? 'ScopedSampleToken',
            $refreshToken ?? 'ScopedSampleRefreshToken'
        )));

        return new ResourceProvider([
            'sandbox' => true,
            'clientId' => 'demo_client',
            'clientSecret' => 'secret_demo_client',
            'redirectUri' => 'http://your-uri',
            'ownerProvider' => $ownerProvider,
        ]);
    }
}
