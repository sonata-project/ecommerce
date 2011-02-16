<?php

namespace Sonata\DeliveryBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;

use Symfony\Component\Config\FileLocator;

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
     * @param array            $config    An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = call_user_func_array('array_merge_recursive', $config);
        
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('delivery.xml');

        $pool_definition = new Definition($config['pool']['class']);

        foreach ($config['pool']['methods'] as $method)
        {
            if (!$method['enabled'])
            {
                continue;
            }

            $definition = new Definition($method['class']);
            $definition->addMethodCall('setName', array($method['name']));
            $definition->addMethodCall('setCode', array($method['id']));
            $definition->addMethodCall('setEnabled', array($method['enabled']));
            $definition->addMethodCall('setOptions', array(isset($method['options']) ? $method['options'] : array()));

            $id         = sprintf('sonata.delivery.method.%s', $method['name']);

            // add the delivery method as a service
            $container->setDefinition(sprintf('sonata.delivery.method.%s', $method['name']), $definition);

            // add the delivery method in the method pool
            $pool_definition->addMethodCall('addMethod', array(new Reference($id)));
        }

        $container->setDefinition('sonata.delivery.pool',$pool_definition);

        $definition = new Definition($config['selector']['class']);
        $definition->addMethodCall('setLogger', array(new Reference('logger')));
        $definition->addMethodCall('setProductPool', array(new Reference('sonata.product.pool')));
        $definition->addMethodCall('setDeliveryPool', array(new Reference('sonata.delivery.pool')));

        $container->setDefinition('sonata.delivery.selector', $definition);
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {

        return __DIR__.'/../Resources/config/schema';
    }

    public function getNamespace()
    {

        return 'http://www.sonata-project.org/schema/dic/sonata-delivery';
    }

    public function getAlias()
    {
        
        return 'sonata_delivery';
    }
}