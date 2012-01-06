<?php

namespace Sonata\DeliveryBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

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
        $loader->load('delivery_orm.xml');

        $pool = $container->getDefinition('sonata.delivery.pool');

        foreach ($config['services'] as $id => $options) {
            $definition = $container->getDefinition($id);

            $enabled = isset($options['enabled']) ? (bool)$options['enabled'] : true;

            if (!$enabled) {
                $container->removeDefinition($id);
                continue;
            }

            $name    = isset($options['name']) ? (string)$options['name'] : "n/a";
            $code      = isset($options['code']) ? $options['code'] : false;

            if (!$code) {
                throw new \RuntimeException('Please provide an id argument to the delivery name');
            }

            $definition->addMethodCall('setName', array($name));
            $definition->addMethodCall('setCode', array($code));
            $definition->addMethodCall('setEnabled', array($enabled));

            // add the delivery method in the method pool
            $pool->addMethodCall('addMethod', array(new Reference($id)));
        }
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