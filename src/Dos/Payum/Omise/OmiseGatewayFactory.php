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

namespace Dos\Payum\Omise;

use Dos\Payum\Omise\Action\AuthorizeAction;
use Dos\Payum\Omise\Action\ConvertPaymentAction;
use Dos\Payum\Omise\Action\CaptureAction;
use Dos\Payum\Omise\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;
use PhpMob\Omise\Client\GuzzleHttpClient;
use PhpMob\Omise\OmiseApi;

/**
 * @author Ishmael Doss <nukboon@gmail.com>
 */
class OmiseGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritdoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $supportedBanks = $config['omise_supported_banks'] ?? [
            'bay' => ['label' => 'Krungsri Online', 'logo' => null],
            'bbl' => ['label' => 'Bualuang iBanking', 'logo' => null],
            'ktb' => ['label' => 'KTB Netbank', 'logo' => null],
            'scb' => ['label' => 'SCB Easy Net', 'logo' => null],
        ];

        $config->defaults([
            'payum.factory_name' => 'omise',
            'payum.factory_title' => 'omise',
            'payum.template.omise_credit_card' => $config['payum.omise.credit_card_template'] ?? '::omiseCreditCard.html.twig',
            'payum.action.authorize' => new AuthorizeAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
            'payum.action.capture' => new CaptureAction(
                $config['omise_public_key'],
                $supportedBanks,
                boolval($config['omise_3d_secure'])
            ),
        ]);

        if (!$config['payum.api']) {
            $config['payum.api'] = function (ArrayObject $config) {
                return new OmiseApi(new GuzzleHttpClient($config['payum.omise.http_client']), [
                    'secret_key' => $config['omise_secret_key'],
                    'public_key' => $config['omise_public_key'],
                    'country_code' => $config['omise_country_code'],
                    'sandbox' => !$config['omise_live_mode'],
                ]);
            };
        }
    }
}
