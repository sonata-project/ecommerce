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

namespace Sonata\DeliveryBundle\DependencyInjection;

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
        $node = $treeBuilder->root('sonata_delivery');

        $node
            ->children()
                ->scalarNode('selector')->defaultValue('sonata.delivery.selector.default')->cannotBeEmpty()->end()
            ->end()
        ;

        $this->addDeliverySection($node);
        $this->addModelSection($node);

        return $treeBuilder;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addDeliverySection(ArrayNodeDefinition $node): void
    {
        $node
            ->validate()
                ->ifTrue(function ($v) {
                    foreach ($v['methods'] as $methodCode => $service) {
                        if (null === $service || '' === $service) {
                            foreach ($v['services'] as $serviceConf) {
                                if ($methodCode === $serviceConf['code']) {
                                    break 2;
                                }
                            }

                            return true;
                        }
                    }

                    return false;
                })
                ->thenInvalid('Custom delivery methods require a service id. Provided delivery methods need to be configured with their method code as key.')
            ->end()
            ->children()
                ->arrayNode('services')
                    ->children()
                        ->arrayNode('free_address_required')
                            ->children()
                                ->scalarNode('name')->defaultValue('free_address_required')->cannotBeEmpty()->end()
                                ->scalarNode('code')->defaultValue('free_address_required')->cannotBeEmpty()->end()
                                ->scalarNode('priority')->defaultValue(10)->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                        ->arrayNode('free_address_not_required')
                            ->children()
                                ->scalarNode('name')->defaultValue('free_address_not_required')->cannotBeEmpty()->end()
                                ->scalarNode('code')->defaultValue('free_address_not_required')->cannotBeEmpty()->end()
                                ->scalarNode('priority')->defaultValue(10)->cannotBeEmpty()->end()
                            ->end()
                        ->end()

                    ->end()
                ->end()

                ->arrayNode('methods')
                    ->useAttributeAsKey('code')
                    ->prototype('scalar')->end()
                ->end()

            ->end()
        ;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addModelSection(ArrayNodeDefinition $node): void
    {
    }
}
