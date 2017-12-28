<?php

declare(strict_types=1);

namespace Tests\Dos\SyliusOmisePlugin\Behat\Mocker;

use Sylius\Behat\Service\Mocker\Mocker;

final class OmiseApiMocker
{
    /**
     * @var Mocker
     */
    private $mocker;

    /**
     * @param Mocker $mocker
     */
    public function __construct(Mocker $mocker)
    {
        $this->mocker = $mocker;
    }

    /**
     * @param callable $action
     */
    public function mockApiSuccessfulPaymentResponse(callable $action)
    {
        $service = $this->mocker
            ->mockService('dos.omise_plugin_api', OpenPayUBridgeInterface::class);

        $service->shouldReceive('create')->andReturn($this->createResponseSuccessfulApi());
        $service->shouldReceive('setAuthorizationDataApi');

        $action();

        $this->mocker->unmockAll();
    }

    /**
     * @param callable $action
     */
    public function completedPayment(callable $action)
    {
        $service = $this->mocker
            ->mockService('bitbag.payu_plugin.bridge.open_payu', OpenPayUBridgeInterface::class);

        $service->shouldReceive('retrieve')->andReturn(
            $this->getDataRetrieve(OpenPayUBridge::COMPLETED_API_STATUS)
        );
        $service->shouldReceive('create')->andReturn($this->createResponseSuccessfulApi());
        $service->shouldReceive('setAuthorizationDataApi');

        $action();

        $this->mocker->unmockAll();
    }

    /**
     * @param callable $action
     */
    public function canceledPayment(callable $action)
    {
        $service = $this->mocker
            ->mockService('bitbag.payu_plugin.bridge.open_payu', OpenPayUBridgeInterface::class);

        $service->shouldReceive('retrieve')->andReturn(
            $this->getDataRetrieve(OpenPayUBridge::CANCELED_API_STATUS)
        );
        $service->shouldReceive('create')->andReturn($this->createResponseSuccessfulApi());
        $service->shouldReceive('setAuthorizationDataApi');

        $action();

        $this->mocker->unmockAll();
    }

    /**
     * @param $statusPayment
     *
     * @return \OpenPayU_Result
     */
    private function getDataRetrieve($statusPayment)
    {
        $openPayUResult = new \OpenPayU_Result();

        $data = (object)[
            'status' => (object)[
                'statusCode' => OpenPayUBridge::SUCCESS_API_STATUS
            ],
            'orderId' => 1,
            'orders' => [
                (object)[
                    'status' => $statusPayment
                ]
            ]
        ];

        $openPayUResult->setResponse($data);

        return $openPayUResult;
    }

    /**
     * @return \OpenPayU_Result
     */
    private function createResponseSuccessfulApi()
    {
        $openPayUResult = new \OpenPayU_Result();

        $data = (object)[
            'status' => (object)[
                'statusCode' => OpenPayUBridge::SUCCESS_API_STATUS
            ],
            'orderId' => 1,
            'redirectUri' => '/'
        ];

        $openPayUResult->setResponse($data);

        return $openPayUResult;
    }
}
