<?php

declare(strict_types=1);

namespace Toro\Pay\Bridge\Sylius;

use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Model\UserOAuthInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Toro\Pay\Provider\TokenProviderInterface;
use Toro\Pay\ToroPay;

final class UserAuthenTokenProvider implements TokenProviderInterface
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
     * {@inheritdoc}
     */
    public function getAccessToken(): ?string
    {
        $user = $this->getUserOAuth();

        return null === $user ? null : $user->getAccessToken();
    }

    /**
     * {@inheritdoc}
     */
    public function getRefreshToken(): ?string
    {
        $user = $this->getUserOAuth();

        return null === $user ? null : $user->getRefreshToken();
    }

    public function setAccessToken(?string $token = null): void
    {
        // TODO: Implement setAccessToken() method.
    }

    public function setRefreshToken(?string $token = null): void
    {
        // TODO: Implement setRefreshToken() method.
    }
}
