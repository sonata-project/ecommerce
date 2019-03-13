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

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * DeliveryExtension.
 *
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SonataDeliveryExtension extends Extension
{
    /**
     * Loads the delivery configuration.
     *
     * @param array            $configs   An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('delivery.xml');
        $loader->load('form.xml');

        $container->setAlias('sonata.delivery.selector', $config['selector']);

        $this->configureDelivery($container, $config);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    public function configureDelivery(ContainerBuilder $container, array $config): void
    {
        $pool = $container->getDefinition('sonata.delivery.pool');

        $internal = [
            'free_address_required' => 'sonata.delivery.method.free_address_required',
            'free_address_not_required' => 'sonata.delivery.method.free_address_not_required',
        ];

        $configured = [];

        foreach ($config['services'] as $id => $settings) {
            if (\array_key_exists($id, $internal)) {
                $id = $internal[$id];

                $definition = $container->getDefinition($id);

                $definition->addMethodCall('setName', [$settings['name']]);
                $definition->addMethodCall('setCode', [$settings['code']]);
                $definition->addMethodCall('setPriority', [$settings['priority']]);

                $configured[$settings['code']] = $id;
            }
        }

        foreach ($config['methods'] as $code => $id) {
            if (\array_key_exists($code, $configured)) {
                // Internal service
                $id = $configured[$code];
            }

            if ($container->hasDefinition($id)) {
                $definition = $container->getDefinition($id);
                $definition->addMethodCall('setEnabled', [true]);
            }

            // add the delivery method in the method pool
            $pool->addMethodCall('addMethod', [new Reference($id)]);
        }

        // Remove unconfigured services
        foreach ($internal as $code => $id) {
            if (false === array_search($id, $configured, true)) {
                $container->removeDefinition($id);
            }
        }
    }
}
