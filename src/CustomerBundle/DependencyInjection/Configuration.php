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

namespace Sonata\CustomerBundle\DependencyInjection;

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
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('sonata_customer');

        $this->addModelSection($node);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addModelSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('customer')->defaultValue('Application\\Sonata\\CustomerBundle\\Entity\\Customer')->end()
                        ->scalarNode('customer_selector')->defaultValue('Sonata\\Component\\Customer\\CustomerSelector')->end()
                        ->scalarNode('address')->defaultValue('Application\\Sonata\\CustomerBundle\\Entity\\Address')->end()
                        ->scalarNode('order')->defaultValue('Application\\Sonata\\OrderBundle\\Entity\\Order')->end()
                        ->scalarNode('user')->defaultValue('Application\\Sonata\\UserBundle\\Entity\\User')->end()
                        ->scalarNode('user_identifier')->defaultValue('id')->end()
                    ->end()
                ->end()
                ->arrayNode('field')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('customer')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('user')->defaultValue('id')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
