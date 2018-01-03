<?php

declare(strict_types=1);

namespace Toro\Pay\Bridge\Sylius;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Model\UserOAuthInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Toro\Pay\Provider\OwnerProviderInterface;
use Toro\Pay\ToroPay;

final class OwnerProvider implements OwnerProviderInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return null|UserOAuthInterface
     */
    private function getUserOAuth(): ?UserOAuthInterface
    {
        if (!$token = $this->tokenStorage->getToken()) {
            return null;
        }

        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return null;
        }

        return $user->getOAuthAccount(ToroPay::SERVICE_NAME);
    }

    /**
     * @return null|string
     */
    private function getAccessToken(): ?string
    {
        $user = $this->getUserOAuth();

        return null === $user ? null : $user->getAccessToken();
    }

    /**
     * @return null|string
     */
    private function getRefreshToken(): ?string
    {
        $user = $this->getUserOAuth();

        return null === $user ? null : $user->getRefreshToken();
    }

    /**
     * @return null|string
     */
    private function getResourceOwinerId(): ?string
    {
        $user = $this->getUserOAuth();

        return null === $user ? null : (string) $user->getUser();
    }

    /**
     * {@inheritdoc}
     */
    public function getToken(): AccessToken
    {
        return new AccessToken([
            'access_token' => $this->getAccessToken(),
            'refresh_token' => $this->getRefreshToken(),
            'resource_owner_id' => $this->getResourceOwinerId(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function store(AccessToken $token, ResourceOwnerInterface $owner): void
    {
        // TODO: Implement setRefreshToken() method.
    }
}
