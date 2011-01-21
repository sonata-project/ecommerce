<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\PaymentBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * UrlShortenerExtension.
 *
 *
 * @author     Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class PaymentExtension extends Extension {

    /**
     * Loads the delivery configuration.
     *
     * @param array            $config    An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function configLoad($config, ContainerBuilder $container) {
        $loader = new XmlFileLoader($container, __DIR__.'/../Resources/config');
        $loader->load('payment.xml');

        // create the payment method pool
        $pool_definition = new Definition('Sonata\Component\Payment\Pool');

        // define the payment method
        foreach($config['methods'] as $code => $method)
        {
            if(!$method['enabled'])
            {
                continue;
            }

            $definition = new Definition($method['class']);
            $definition->addMethodCall('setName', array($method['name']));
            $definition->addMethodCall('setCode', array($method['id']));
            $definition->addMethodCall('setEnabled', array($method['enabled']));
            $definition->addMethodCall('setOptions', array(isset($method['options']) ? $method['options'] : array()));
            $definition->addMethodCall('setTranslator', array(new Reference('translator')));

            foreach((array)$method['transformers'] as $name => $service_id) {
                $definition->addMethodCall('addTransformer', array($name, new Reference($service_id)));
            }

            // todo : refactor this into proper files
            foreach((array)$method['dependencies'] as $service_id => $setter) {

                $definition->addMethodCall($setter, array(new Reference($service_id)));
            }

            $definition->addMethodCall('setRouter', array(new Reference('router')));

            $id         = sprintf('sonata.payment.method.%s', $method['name']);

            // add the delivery method as a service
            $container->setDefinition($id, $definition);

            // add the delivery method in the method pool
            $pool_definition->addMethodCall('addMethod', array(new Reference($id)));
        }

        $container->setDefinition('sonata.payment.pool', $pool_definition);

        // define the payment selector
        $definition = new Definition($config['selector']['class']);
        $definition->addMethodCall('setLogger', array(new Reference('logger')));
        $definition->addMethodCall('setProductPool', array(new Reference('sonata.product.pool')));
        $definition->addMethodCall('setPaymentPool', array(new Reference('sonata.payment.pool')));

        $container->setDefinition('sonata.payment.selector', $definition);

    }

    public function transformerLoad($config, ContainerBuilder $container) {

        $loader = new XmlFileLoader($container, __DIR__.'/../Resources/config');
        $loader->load('transformer.xml');

        $pool_definition = new Definition('Sonata\Component\Transformer\Pool');

        foreach($config['types'] as $type)
        {
            if(!$type['enabled'])
            {
                continue;
            }

            $definition = new Definition($type['class']);
            $definition->addMethodCall('setLogger', array(new Reference('logger')));
            $definition->addMethodCall('setOptions', array($type));
            $definition->addMethodCall('setProductPool', array(new Reference('sonata.product.pool')));

            $id         = sprintf('sonata.transformer.%s', $type['id']);

            // add the delivery method as a service
            $container->setDefinition($id, $definition);

            // add the delivery method in the method pool
            $pool_definition->addMethodCall('addTransformer', array($type['id'], new Reference($id)));
        }

        $container->setDefinition('sonata.transformer', $pool_definition);
    }

    public function generatorLoad($config, ContainerBuilder $container) {

        $loader = new XmlFileLoader($container, __DIR__.'/../Resources/config');
        $loader->load('generator.xml');

        $definition = new Definition($config['class']);
        $definition->addMethodCall('setEntityManager', array(new Reference('doctrine.orm.default_entity_manager')));

        $container->setDefinition('sonata.generator', $definition);
    }


    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath() {

        return __DIR__.'/../Resources/config/schema';
    }

    public function getNamespace() {

        return 'http://www.sonata-project.org/schema/dic/sonata-payment';
    }

    public function getAlias() {
        
        return 'sonata_payment';
    }
}