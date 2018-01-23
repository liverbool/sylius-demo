<?php

declare(strict_types=1);

namespace Toro\SyliusToroPayPlugin\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Toro\Payum\Request\DisplayFailure;
use Toro\SyliusToroPayPlugin\Request\ToroConnectRequest;

class FlashDisplayErrorAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(TokenStorageInterface $tokenStorage, ?FlashBagInterface $flashBag)
    {
        $this->tokenStorage = $tokenStorage;
        $this->flashBag = $flashBag;
    }

    /**
     * @return bool
     */
    private function isAuthenticated(): bool
    {
        if (!$token = $this->tokenStorage->getToken()) {
            return false;
        }

        return $token->getUser() instanceof UserInterface;
    }

    /**
     * @param DisplayFailure $request
     *
     * @throws \Payum\Core\Reply\ReplyInterface
     */
    public function execute($request)
    {
        $error = $request->getError();

        // TODO should error.isNotLogin
        if ($error->isAccessDenied()) {
            $model = ArrayObject::ensureArrayObject($request->getModel());

            $this->gateway->execute(new ToroConnectRequest($model));

            return;
        }

        if ($this->flashBag) {
            $messages = array_filter(array_merge([$request->getFailureReason()], $request->getErrors()));
            $this->flashBag->set('error', sprintf('<ul><li>%s</li></ul>', implode('</li><li>', $messages)));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return $request instanceof DisplayFailure;
    }
}
