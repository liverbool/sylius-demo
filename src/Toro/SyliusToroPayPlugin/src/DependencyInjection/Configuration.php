<?php

declare(strict_types=1);

namespace Toro\SyliusToroPayPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_toro_pay');

        $rootNode
            ->children()
                ->scalarNode('connect_uri')->defaultValue('/connect/toropay')->cannotBeEmpty()->end()
                ->scalarNode('firewall')->defaultValue('shop')->cannotBeEmpty()->end()
            ->end()
            ->children()
                ->arrayNode('http')
                ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('client')->defaultValue('httplug.client.default')->end()
                        ->scalarNode('message_factory')->defaultValue('httplug.message_factory.default')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
