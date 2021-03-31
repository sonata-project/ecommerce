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

namespace Sonata\OrderBundle\DependencyInjection;

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
        $treeBuilder = new TreeBuilder('sonata_order');
        $node = $treeBuilder->getRootNode();

        $this->addModelSection($node);

        return $treeBuilder;
    }

    private function addModelSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('order')->defaultValue('App\\Sonata\\OrderBundle\\Entity\\Order')->end()
                        ->scalarNode('order_element')->defaultValue('App\\Sonata\\OrderBundle\\Entity\\OrderElement')->end()
                        ->scalarNode('customer')->defaultValue('App\\Sonata\\CustomerBundle\\Entity\\Customer')->end()
                    ->end()
                ->end()
            ->end();
    }
}
