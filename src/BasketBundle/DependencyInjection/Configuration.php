<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\BasketBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('sonata_basket');

        $node
            ->children()
                ->scalarNode('builder')->defaultValue('sonata.basket.builder.standard')->cannotBeEmpty()->end()
                ->scalarNode('factory')->defaultValue('sonata.basket.session.factory')->cannotBeEmpty()->end()
                ->scalarNode('loader')->defaultValue('sonata.basket.loader.standard')->cannotBeEmpty()->end()
            ->end()
        ;

        $this->addModelSection($node);
        $this->addFormSection($node);

        return $treeBuilder;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addModelSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('basket')->defaultValue('Application\\Sonata\\BasketBundle\\Entity\\Basket')->end()
                        ->scalarNode('basket_element')->defaultValue('Application\\Sonata\\BasketBundle\\Entity\\BasketElement')->end()
                        ->scalarNode('customer')->defaultValue('Application\\Sonata\\CustomerBundle\\Entity\\Customer')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addFormSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('basket')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('form')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('sonata_basket_basket')->end()
                                ->scalarNode('name')->defaultValue('sonata_basket_basket_form')->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('shipping')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('form')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('sonata_basket_shipping')->end()
                                ->scalarNode('name')->defaultValue('sonata_basket_shipping_form')->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('payment')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('form')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('sonata_basket_payment')->end()
                                ->scalarNode('name')->defaultValue('sonata_basket_payment_form')->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
