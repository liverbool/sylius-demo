<?php

declare(strict_types=1);

namespace Toro\SyliusToroPayPlugin\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Authorize;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetHttpRequest;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Toro\SyliusToroPayPlugin\Request\ToroConnectRequest;

class ToroConnectAction implements ActionInterface, GatewayAwareInterface
{
    use TargetPathTrait;
    use GatewayAwareTrait;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var string
     */
    private $connectUri;

    /**
     * @var string
     */
    private $firewallProviderKey;

    public function __construct(SessionInterface $session, string $connectUri, string $firewallProviderKey)
    {
        $this->session = $session;
        $this->connectUri = $connectUri;
        $this->firewallProviderKey = $firewallProviderKey;
    }

    /**
     * {@inheritdoc}
     *
     * @param Authorize $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        // request for current uri (captureUri)
        $this->gateway->execute($httpRequest = new GetHttpRequest());

        // authorize success return
        // @see \Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler
        $this->saveTargetPath($this->session, $this->firewallProviderKey, $httpRequest->uri);

        throw new HttpRedirect($this->connectUri);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return $request instanceof ToroConnectRequest;
    }
}
