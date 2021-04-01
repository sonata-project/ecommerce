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

namespace Sonata\ProductBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('sonata_product');
        $node = $treeBuilder->getRootNode();

        $this->addProductSection($node);
        $this->addModelSection($node);
        $this->addSeoSection($node);

        return $treeBuilder;
    }

    private function addProductSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('products')
                    ->useAttributeAsKey('id')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('provider')->isRequired()->end()
                            ->scalarNode('manager')->isRequired()->end()
                            ->arrayNode('variations')
                                ->children()
                                    ->arrayNode('fields')
                                        ->isRequired()
                                        ->prototype('scalar')->end()
                                    ->end()
                                ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addModelSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('product')->defaultValue('App\\Sonata\\ProductBundle\\Entity\\Product')->end()
                        ->scalarNode('package')->defaultValue('App\\Sonata\\ProductBundle\\Entity\\Package')->end()
                        ->scalarNode('product_category')->defaultValue('App\\Sonata\\ProductBundle\\Entity\\ProductCategory')->end()
                        ->scalarNode('product_collection')->defaultValue('App\\Sonata\\ProductBundle\\Entity\\ProductCollection')->end()
                        ->scalarNode('category')->defaultValue('App\\Sonata\\ClassificationBundle\\Entity\\Category')->end()
                        ->scalarNode('collection')->defaultValue('App\\Sonata\\ClassificationBundle\\Entity\\Collection')->end()
                        ->scalarNode('delivery')->defaultValue('App\\Sonata\\ProductBundle\\Entity\\Delivery')->end()
                        ->scalarNode('media')->defaultValue('App\\Sonata\\MediaBundle\\Entity\\Media')->end()
                        ->scalarNode('gallery')->defaultValue('App\\Sonata\\MediaBundle\\Entity\\Gallery')->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addSeoSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('seo')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('product')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('site')->defaultValue('@sonataproject')->end()
                                ->scalarNode('creator')->defaultValue('@th0masr')->end()
                                ->scalarNode('domain')->defaultValue('http://demo.sonata-project.org')->end()
                                ->scalarNode('media_prefix')->defaultValue('http://demo.sonata-project.org')->end()
                                ->scalarNode('media_format')->defaultValue('reference')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
