<?php

/*
 * This file is part of the PhpMob package.
 *
 * (c) Ishmael Doss <nukboon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Dos\Payum\Omise\Action;

use Dos\Payum\Omise\Request\DisplayFailure;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use PhpMob\Omise\Domain\Charge;

/**
 * @author Ishmael Doss <nukboon@gmail.com>
 */
class StatusAction implements ActionInterface, ApiAwareInterface, GatewayAwareInterface
{
    use ApiAwareTrait;
    use GatewayAwareTrait;

    /**
     * {@inheritdoc}
     *
     * @param GetStatusInterface $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());
        $status = $model['status'];

        // verify last status
        if ($model['chargeId'] && Charge::STATUS_PENDING === $status) {
            $charge = $this->api->charge->find($model['chargeId']);
            $model['failureCode'] = $charge->failureCode;
            $model['failureMessage'] = $charge->failureMessage;
            $status = $model['status'] = $charge->status;

            if ($model['failureCode']) {
                $this->gateway->execute(new DisplayFailure($model));
            }
        }

        switch ($status) {
            case false:
                $request->markNew();
                break;
            case Charge::STATUS_PENDING:
                $request->markPending();
                break;
            case Charge::STATUS_SUCCESSFUL:
                $request->markCaptured();
                break;
            case Charge::STATUS_FAILED:
                $request->markFailed();
                break;
            default:
                $request->markUnknown();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
