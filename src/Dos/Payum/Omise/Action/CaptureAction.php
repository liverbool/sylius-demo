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
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Authorize;
use Payum\Core\Request\Capture;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\ObtainCreditCard;
use PhpMob\Omise\Domain\Charge;
use PhpMob\Omise\Domain\Source;
use PhpMob\Omise\Exception\InvalidResponseException;

/**
 * @author Ishmael Doss <nukboon@gmail.com>
 */
class CaptureAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use ApiAwareTrait;
    use GatewayAwareTrait;

    /**
     * @var string
     */
    private $publicKey;

    /**
     * @var array
     */
    private $supportedBanks = [];

    /**
     * @var bool
     */
    private $enable3DSecure = true;

    public function __construct(string $publicKey, array $supportedBanks, bool $enable3DSecure = true)
    {
        $this->publicKey = $publicKey;
        $this->supportedBanks = $supportedBanks;
        $this->enable3DSecure = $enable3DSecure;
    }

    /**
     * @param Capture $request
     *
     * @throws \Payum\Core\Reply\ReplyInterface
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $this->gateway->execute($httpRequest = new GetHttpRequest());

        $model = ArrayObject::ensureArrayObject($request->getModel());
        $model['publicKey'] = $this->publicKey;
        $model['supportedBanks'] = $this->supportedBanks;

        if ($securityToken = $request->getToken()) {
            $model['afterUrl'] = $securityToken->getAfterUrl();
        }

        $omiseToken = @$httpRequest->request['omiseToken'];

        // request for credit card
        if (empty($omiseToken)) {
            if ('POST' === strtoupper($httpRequest->method)) {
                $model['invalid'] = true;
            }

            $this->gateway->execute(new ObtainCreditCard($model));
        }

        $charge = new Charge();
        $charge->amount = ceil($model['amount'] / 100);
        $charge->currency = $model['currency'];
        $charge->description = $model['description'];
        // Internet banks or 3D-Secure https://www.omise.co/th/how-to-implement-3-D-Secure
        $charge->returnUri = $model['afterUrl'];

        // internet banking
        if (in_array($omiseToken, array_keys($this->supportedBanks))) {
            $this->createInternetBankingCharge($omiseToken, $charge, $model);
        } else {
            $this->createCreditCharge($omiseToken, $charge, $model);
        }
    }

    /**
     * @param string $omiseToken
     * @param Charge $charge
     * @param ArrayObject $model
     *
     * @throws \Payum\Core\Reply\ReplyInterface
     */
    private function createCreditCharge(string $omiseToken, Charge $charge, ArrayObject $model): void
    {
        // let create charge for credit
        try {
            $charge->cardToken = $omiseToken;
            $this->api->charge->createUsingToken($charge);

            $model['status'] = $charge->status;
            $model['chargeId'] = $charge->id;

        } catch (InvalidResponseException $e) {
            $model['failureCode'] = $e->error->code;
            $model['failureMessage'] = $e->error->message;

            $this->gateway->execute(new DisplayFailure($model));

            return;
        }

        // 3D-Secure authen
        if ($charge->authorizeUri/* && $this->enable3DSecure*/) {
            $model['authorizeUri'] = $charge->authorizeUri;

            $this->gateway->execute(new Authorize($model));
        }
    }

    /**
     * @param string $omiseToken
     * @param Charge $charge
     * @param ArrayObject $model
     *
     * @throws \Payum\Core\Reply\ReplyInterface
     */
    private function createInternetBankingCharge(string $omiseToken, Charge $charge, ArrayObject $model): void
    {
        try {
            $source = new Source();
            $source->amount = $charge->amount;
            $source->currency = $charge->currency;
            $source->type = 'internet_banking_' . $omiseToken;

            $this->api->source->create($source);

            $charge->source = $source;

            $this->api->charge->createUsingSource($charge);

            $model['status'] = $charge->status;
            $model['chargeId'] = $charge->id;
            $model['authorizeUri'] = $charge->authorizeUri;

            $this->gateway->execute(new Authorize($model));

        } catch (InvalidResponseException $e) {
            $model['failureCode'] = $e->error->code;
            $model['failureMessage'] = $e->error->message;

            $this->gateway->execute(new DisplayFailure($model));

            return;
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
