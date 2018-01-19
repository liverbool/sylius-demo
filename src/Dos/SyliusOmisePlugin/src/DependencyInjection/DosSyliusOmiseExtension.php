<?php

/*
 * This file is part of the Dos package.
 *
 * (c) Ishmael Doss <nukboon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Dos\SyliusOmisePlugin\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class DosSyliusOmiseExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.xml');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);
        $payumConfigs = $container->getExtensionConfig('payum');
        $payumCoreConfigs = $payumConfigs[0]['gateways']['core'] ?? [];
        $payumCoreConfigs = array_replace_recursive($payumCoreConfigs, [
            'omise_supported_banks' => empty($config['supported_banks']) ? null : $config['supported_banks'],
            'payum.omise.credit_card_template' => $config['credit_card_template'],
        ]);

        $container->prependExtensionConfig('payum', [
            'gateways' => [
                'core' => $payumCoreConfigs,
            ]
        ]);
    }
}
