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

namespace Sonata\ProfileBundle\DependencyInjection;

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
        $node = $treeBuilder->root('sonata_profile');

        $node
            ->children()
                ->scalarNode('template')->defaultValue('SonataProfileBundle::action.html.twig')->end()
            ->end()
        ;
        $this->addDashboardSection($node);
        $this->addMenuSection($node);

        return $treeBuilder;
    }

    /**
     * Returns default values for profile menu (to avoid BC Break).
     */
    protected function getProfileMenuDefaultValues(): array
    {
        return [
            [
                'route' => 'sonata_profile_dashboard',
                'label' => 'link_list_dashboard',
                'domain' => 'SonataProfileBundle',
                'route_parameters' => [],
            ],
            [
                'route' => 'sonata_customer_addresses',
                'label' => 'link_list_addresses',
                'domain' => 'SonataCustomerBundle',
                'route_parameters' => [],
            ],
            [
                'route' => 'sonata_order_index',
                'label' => 'order_list',
                'domain' => 'SonataOrderBundle',
                'route_parameters' => [],
            ],
        ];
    }

    private function addDashboardSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('dashboard')
                    ->addDefaultsIfNotSet()
                    ->fixXmlConfig('group')
                    ->fixXmlConfig('block')
                        ->children()
                        ->arrayNode('groups')
                            ->useAttributeAsKey('id')
                            ->prototype('array')
                            ->fixXmlConfig('item')
                            ->fixXmlConfig('item_add')
                            ->children()
                                ->scalarNode('label')->end()
                                ->scalarNode('label_catalogue')->end()
                                ->arrayNode('items')
                                    ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('item_adds')
                                    ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('roles')
                                    ->prototype('scalar')->defaultValue([])->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('blocks')
                        ->defaultValue([['position' => 'left', 'settings' => ['content' => '<h2>Welcome!</h2> This is a sample shop profile dashboard, feel free to override it in the configuration!'], 'type' => 'sonata.block.service.text']])
                        ->prototype('array')
                            ->fixXmlConfig('setting')
                                ->children()
                                ->scalarNode('type')->cannotBeEmpty()->end()
                                ->arrayNode('settings')
                                    ->useAttributeAsKey('id')
                                    ->prototype('variable')->defaultValue([])->end()
                                ->end()
                                ->scalarNode('position')->defaultValue('right')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addMenuSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('menu')
                    ->prototype('array')
                        ->addDefaultsIfNotSet()
                        ->cannotBeEmpty()
                        ->children()
                            ->scalarNode('route')->cannotBeEmpty()->end()
                            ->arrayNode('route_parameters')
                                ->defaultValue([])
                                ->prototype('array')->end()
                            ->end()
                            ->scalarNode('label')->cannotBeEmpty()->end()
                            ->scalarNode('domain')->defaultValue('messages')->end()
                        ->end()
                    ->end()
                    ->defaultValue($this->getProfileMenuDefaultValues())
                ->end()
            ->end()
        ;
    }
}
