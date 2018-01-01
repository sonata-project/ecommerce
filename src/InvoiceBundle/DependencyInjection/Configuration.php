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

namespace Sonata\InvoiceBundle\DependencyInjection;

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
        $node = $treeBuilder->root('sonata_invoice');

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
                        ->scalarNode('invoice')->defaultValue('Application\\Sonata\\InvoiceBundle\\Entity\\Invoice')->end()
                        ->scalarNode('invoice_element')->defaultValue('Application\\Sonata\\InvoiceBundle\\Entity\\InvoiceElement')->end()

                        ->scalarNode('order_element')->defaultValue('Application\\Sonata\\OrderBundle\\Entity\\OrderElement')->end()
                        ->scalarNode('customer')->defaultValue('Application\\Sonata\\CustomerBundle\\Entity\\Customer')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
