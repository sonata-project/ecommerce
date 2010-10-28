<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\PaymentBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;

/**
 * UrlShortenerExtension.
 *
 *
 * @author     Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class PaymentExtension extends Extension
{

    /**
     * Loads the delivery configuration.
     *
     * @param array            $config    An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function paymentLoad($config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, __DIR__.'/../Resources/config');
        $loader->load('payment.xml');

        $pool_definition = new Definition('Sonata\Component\Payment\Pool');

        foreach($config['methods'] as $method)
        {
            $definition = new Definition($method['class']);
            $definition->addMethodCall('setName', array($method['name']));
            $definition->addMethodCall('setOptions', array(isset($method['options']) ? $method['options'] : array()));

            $id         = sprintf('sonata.payment.method.%s', $method['name']);

            // add the delivery method as a service
            $container->setDefinition(sprintf('sonata.payment.method.%s', $method['name']), $definition);

            // add the delivery method in the method pool
            $pool_definition->addMethodCall('addMethod', array(new Reference($id)));
        }

        $container->setDefinition('sonata.payment.pool',$pool_definition);
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
        return 'http://www.sonata-project.org/schema/dic/sonata-payment';
    }

    public function getAlias()
    {
        return 'sonata-payment';
    }
}