<?php

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
        $node = $treeBuilder->root('sonata_product');

        $this->addProductSection($node);
        $this->addModelSection($node);
        $this->addSeoSection($node);
        $this->addSlugifySection($node);

        return $treeBuilder;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addProductSection(ArrayNodeDefinition $node)
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
            ->end()
        ;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addModelSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('product')->defaultValue('Application\\Sonata\\ProductBundle\\Entity\\Product')->end()
                        ->scalarNode('package')->defaultValue('Application\\Sonata\\ProductBundle\\Entity\\Package')->end()
                        ->scalarNode('product_category')->defaultValue('Application\\Sonata\\ProductBundle\\Entity\\ProductCategory')->end()
                        ->scalarNode('product_collection')->defaultValue('Application\\Sonata\\ProductBundle\\Entity\\ProductCollection')->end()
                        ->scalarNode('category')->defaultValue('Application\\Sonata\\ClassificationBundle\\Entity\\Category')->end()
                        ->scalarNode('collection')->defaultValue('Application\\Sonata\\ClassificationBundle\\Entity\\Collection')->end()
                        ->scalarNode('delivery')->defaultValue('Application\\Sonata\\ProductBundle\\Entity\\Delivery')->end()
                        ->scalarNode('media')->defaultValue('Application\\Sonata\\MediaBundle\\Entity\\Media')->end()
                        ->scalarNode('gallery')->defaultValue('Application\\Sonata\\MediaBundle\\Entity\\Gallery')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addSeoSection(ArrayNodeDefinition $node)
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

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addSlugifySection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('slugify_service')
                    ->defaultValue('sonata.core.slugify.native') # you should use: sonata.core.slugify.cocur
                ->end()
            ->end();
    }
}
