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

namespace Sonata\CustomerBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sonata\CustomerBundle\DependencyInjection\Configuration;

final class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    public function getConfiguration(): Configuration
    {
        return new Configuration();
    }

    public function testDefault(): void
    {
        $this->assertProcessedConfigurationEquals([
            [],
        ], [
            'class' => [
                'customer' => 'App\\Sonata\\CustomerBundle\\Entity\\Customer',
                'customer_selector' => 'Sonata\\Component\\Customer\\CustomerSelector',
                'address' => 'App\\Sonata\\CustomerBundle\\Entity\\Address',
                'order' => 'App\\Sonata\\OrderBundle\\Entity\\Order',
                'user' => 'App\\Sonata\\UserBundle\\Entity\\User',
                'user_identifier' => 'id',
            ],
            'field' => [
                'customer' => [
                    'user' => 'id',
                ],

            ],
            'profile' => [
                'template' => '@SonataCustomer/Profile/action.html.twig',
                'menu_builder' => 'sonata.customer.profile.menu_builder.default',
                'blocks' => [
                    [
                        'position' => 'left',
                        'type' => 'sonata.order.block.recent_orders',
                        'settings' => ['title' => 'Recent Orders', 'number' => 5, 'mode' => 'public'],
                    ],
                    [
                        'position' => 'right',
                        'type' => 'sonata.news.block.recent_posts',
                        'settings' => ['title' => 'Recent Posts', 'number' => 5, 'mode' => 'public'],
                    ],
                    [
                        'position' => 'right',
                        'type' => 'sonata.news.block.recent_comments',
                        'settings' => ['title' => 'Recent Comments', 'number' => 5, 'mode' => 'public'],
                    ],
                ],
                'menu' => [
                    [
                        'route' => 'sonata_customer_dashboard',
                        'label' => 'link_list_dashboard',
                        'domain' => 'SonataCustomerBundle',
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
                ],
            ],
        ]);
    }
}
