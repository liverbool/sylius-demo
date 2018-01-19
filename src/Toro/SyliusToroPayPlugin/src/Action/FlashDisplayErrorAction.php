<?php

declare(strict_types=1);

namespace Toro\SyliusToroPayPlugin\Action;

use Payum\Core\Action\ActionInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Toro\Payum\Request\DisplayFailure;

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
