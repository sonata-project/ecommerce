<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\BasketBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
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

        return $treeBuilder;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     * @return void
     */
    private function addModelSection(ArrayNodeDefinition $node)
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
}
