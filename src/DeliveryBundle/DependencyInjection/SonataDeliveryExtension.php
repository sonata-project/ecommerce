<?php

namespace Sonata\DeliveryBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;

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
 * @author     Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SonataDeliveryExtension extends Extension
{

    /**
     * Loads the delivery configuration.
     *
     * @param array            $configs   An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('delivery.xml');
        $loader->load('form.xml');

        $container->setAlias('sonata.delivery.selector', $config['selector']);

        $this->configureDelivery($container, $config['services']);
    }

    /**
     * @param  \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param  array                                                   $services
     * @return void
     */
    public function configureDelivery(ContainerBuilder $container, array $services)
    {
        $pool = $container->getDefinition('sonata.delivery.pool');

        $implemented = array(
            'free_address_required'     => 'sonata.delivery.method.free_address_required',
            'free_address_not_required' => 'sonata.delivery.method.free_address_not_required',
        );

        foreach ($implemented as $key => $id) {
            if (!isset($services[$key]) || $services[$key]['enabled'] == false) {
                $container->removeDefinition($id);
                continue;
            }

            $definition = $container->getDefinition($id);

            $definition->addMethodCall('setName', array($services[$key]['name']));
            $definition->addMethodCall('setCode', array($services[$key]['code']));
            $definition->addMethodCall('setEnabled', array($services[$key]['enabled']));
            $definition->addMethodCall('setPriority', array($services[$key]['priority']));

            // add the delivery method in the method pool
            $pool->addMethodCall('addMethod', array(new Reference($id)));
        }
    }
}
