<?php

declare(strict_types=1);

namespace Dos\SyliusOmisePlugin\Action;

use Dos\Payum\Omise\Request\DisplayFailure;
use Payum\Core\Action\ActionInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class FlashDisplayErrorAction implements ActionInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(?FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    /**
     * @param DisplayFailure $request
     */
    public function execute($request)
    {
        $this->flashBag && $this->flashBag->set('error', $request->getFailureCode());
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return $request instanceof DisplayFailure;
    }
}
