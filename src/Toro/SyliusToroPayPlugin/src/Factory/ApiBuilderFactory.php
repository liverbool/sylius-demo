<?php

namespace Toro\SyliusToroPayPlugin\Factory;

use Http\Client\HttpClient;
use Sylius\Bundle\PayumBundle\Model\GatewayConfig;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Toro\Pay\Provider\OwnerProviderInterface;
use Toro\Pay\ToroPay;

class ApiBuilderFactory
{
    /**
     * @var RepositoryInterface
     */
    private $configRepository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->configRepository = $repository;
    }

    /**
     * @param OwnerProviderInterface $ownerProvider
     * @param HttpClient $httpClient
     *
     * @return ToroPay
     */
    public function createApi(OwnerProviderInterface $ownerProvider, ?HttpClient $httpClient): ToroPay
    {
        /** @var GatewayConfig $config */
        $config = $this->configRepository->findOneBy(['gatewayName' => ToroPay::SERVICE_NAME]);
        $config = $config->getConfig();

        return new ToroPay([
            'sandbox' => $config['toropay_sandbox'],
            'clientId' => $config['toropay_client_id'],
            'clientSecret' => $config['toropay_client_secret'],
            'redirectUri' => $config['toropay_redirect_uri'],
            'httpClient' => $httpClient,
            'ownerProvider' => $ownerProvider,
        ]);
    }
}
