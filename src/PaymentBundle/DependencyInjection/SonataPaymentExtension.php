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
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;

/**
 *
 * @author     Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SonataPaymentExtension extends Extension
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
        $loader->load('consumer.xml');
        $loader->load('orm.xml');
        $loader->load('payment.xml');
        $loader->load('generator.xml');
        $loader->load('transformer.xml');
        $loader->load('selector.xml');
        $loader->load('browser.xml');
        $loader->load('form.xml');

        $this->registerDoctrineMapping($config);
        $this->registerParameters($container, $config);
        $this->configurePayment($container, $config);
        $this->configureSelector($container, $config['selector']);
        $this->configureTransformer($container, $config['transformers']);

        $container->setAlias('sonata.generator', $config['generator']);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param $config
     * @return void
     */
    public function registerParameters(ContainerBuilder $container, array $config)
    {
        $container->setParameter('sonata.payment.transaction.class', $config['class']['transaction']);
    }

    /**
     * @throws \RuntimeException
     * @param  ContainerBuilder $container
     * @param  array            $config
     * @return void
     */
    public function configurePayment(ContainerBuilder $container, array $config)
    {
        // create the payment method pool
        $pool = $container->getDefinition('sonata.payment.pool');

        $internal = array(
            'debug'    => 'sonata.payment.method.debug',
            'pass'     => 'sonata.payment.method.pass',
            'check'    => 'sonata.payment.method.check',
            'scellius' => 'sonata.payment.method.scellius',
            'ogone'    => 'sonata.payment.method.ogone',
            'paypal'   => 'sonata.payment.method.paypal',
        );

        $configured = array();

        // define the payment method
        foreach ($config['services'] as $id => $settings) {
            if (array_key_exists($id, $internal)) {
                $id = $internal[$id];

                $name     = isset($settings['name']) ? $settings['name'] : 'n/a';
                $options  = isset($settings['options']) ? $settings['options'] : array();

                $code  = isset($settings['code']) ? $settings['code'] : false;

                if (!$code) {
                    throw new \RuntimeException('Please provide a code for the payment handler');
                }

                $definition = $container->getDefinition($id);

                $definition->addMethodCall('setName', array($name));
                $definition->addMethodCall('setOptions', array($options));
                $definition->addMethodCall('setCode', array($code));

                foreach ((array) $settings['transformers'] as $name => $serviceId) {
                    $definition->addMethodCall('addTransformer', array($name, new Reference($serviceId)));
                }

                $configured[$code] = $id;
            }
        }

        foreach ($config['methods'] as $code => $id) {
            if (array_key_exists($code, $configured)) {
                // Internal service
                $id = $configured[$code];
            }

            if ($container->hasDefinition($id)) {
                $definition = $container->getDefinition($id);
                $definition->addMethodCall('setEnabled', array(true));
            }

            $pool->addMethodCall('addMethod', array(new Reference($id)));
        }

        if (isset($config['services']['debug'])) {
            $container->getDefinition('sonata.payment.method.debug')
                ->replaceArgument(1, new Reference($config['services']['debug']['browser']));
        }

        if (isset($config['services']['pass'])) {
            $container->getDefinition('sonata.payment.method.pass')
                ->replaceArgument(1, new Reference($config['services']['pass']['browser']));
        }

        if (isset($config['services']['check'])) {
            $container->getDefinition('sonata.payment.method.check')
                ->replaceArgument(2, new Reference($config['services']['check']['browser']));
        }

        if (isset($config['services']['scellius'])) {
            $container->getDefinition('sonata.payment.method.scellius')
                ->replaceArgument(3, new Reference($config['services']['scellius']['generator']));
        }

        // Remove unconfigured services
        foreach ($internal as $code => $id) {
            if (false === array_search($id, $configured)) {
                $container->removeDefinition($id);
            }
        }
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param $selector
     * @return void
     */
    public function configureSelector(ContainerBuilder $container, $selector)
    {
        $container->setAlias('sonata.payment.selector', $selector);
    }

    /**
     * @param  \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param  array                                                   $transformers
     * @return void
     */
    public function configureTransformer(ContainerBuilder $container, array $transformers)
    {
        $pool = $container->getDefinition('sonata.payment.transformer.pool');

        foreach ($transformers as $type => $id) {
            $pool->addMethodCall('addTransformer', array($type, new Reference($id)));
        }
    }

    /**
     * @param  array $config
     * @return void
     */
    public function registerDoctrineMapping(array $config)
    {
        if (!class_exists($config['class']['transaction'])) {
            return;
        }

        $collector = DoctrineCollector::getInstance();

        $collector->addAssociation($config['class']['transaction'], 'mapManyToOne', array(
            'fieldName'    => 'order',
            'targetEntity' => $config['class']['order'],
            'cascade'      => array(),
            'mappedBy'     => NULL,
            'inversedBy'   => NULL,
            'joinColumns'  => array(
                array(
                    'name' => 'order_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'SET NULL',
                ),
            ),
            'orphanRemoval' => false,
        ));

        $collector->addIndex($config['class']['transaction'], 'status_code', array(
            'status_code'
        ));

        $collector->addIndex($config['class']['transaction'], 'state', array(
            'state'
        ));
    }

}
