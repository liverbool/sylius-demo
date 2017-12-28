<?php

declare(strict_types=1);

namespace Dos\SyliusOmisePlugin\DependencyInjection;

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
        $rootNode = $treeBuilder->root('dos_sylius_omise');

        $rootNode
            ->children()
                ->scalarNode('credit_card_template')
                    ->defaultValue('@DosSyliusOmisePlugin/creditCard.html.twig')
                ->end()
                ->arrayNode('supported_banks')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('label')->cannotBeEmpty()->end()
                            ->scalarNode('logo')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
