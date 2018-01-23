<?php

declare(strict_types=1);

namespace Toro\SyliusToroPayPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusToroPayExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $container->setParameter('toropay.connect_uri', $config['connect_uri']);
        $container->setParameter('toropay.firewall', $config['firewall']);

        $this->createHttplugClient($container, $config);

        $loader->load('services.xml');
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    protected function createHttplugClient(ContainerBuilder $container, array $config)
    {
        $httpClientId = $config['http']['client'];
        $httpMessageFactoryId = $config['http']['message_factory'];
        $bundles = $container->getParameter('kernel.bundles');

        if ('httplug.client.default' === $httpClientId && !isset($bundles['HttplugBundle'])) {
            throw new InvalidConfigurationException(
                'You must setup php-http/httplug-bundle to use the default http client service.'
            );
        }

        if ('httplug.message_factory.default' === $httpMessageFactoryId && !isset($bundles['HttplugBundle'])) {
            throw new InvalidConfigurationException(
                'You must setup php-http/httplug-bundle to use the default http message factory service.'
            );
        }

        $container->setAlias('toropay.http.client', new Alias($config['http']['client'], true));
        $container->setAlias('toropay.http.message_factory', new Alias($config['http']['message_factory'], true));
    }
}
