<?php

declare(strict_types=1);

namespace Toro\SyliusToroPayPlugin\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Authorize;
use Payum\Core\Exception\RequestNotSupportedException;
use Toro\SyliusToroPayPlugin\Request\ToroConnectRequest;

class ToroConnectAction implements ActionInterface
{
    /**
     * @var string
     */
    private $connectUri;

    public function __construct(string $connectUri)
    {
        $this->connectUri = $connectUri;
    }

    /**
     * {@inheritdoc}
     *
     * @param Authorize $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

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
