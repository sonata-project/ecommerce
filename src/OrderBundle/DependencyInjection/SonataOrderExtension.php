<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\OrderBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;

/**
 * @author     Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SonataOrderExtension extends Extension
{

    /**
     * Loads the order configuration.
     *
     * @param array            $configs   An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $bundles = $container->getParameter('kernel.bundles');

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('block.xml');
        $loader->load('orm.xml');
        $loader->load('form.xml');
        $loader->load('twig.xml');

        if (isset($bundles['FOSRestBundle']) && isset($bundles['NelmioApiDocBundle'])) {
            $loader->load('api_controllers.xml');
            $loader->load('serializer.xml');
        }

        if (isset($bundles['SonataAdminBundle'])) {
            $loader->load('admin.xml');
        }

        if (isset($bundles['SonataSeoBundle'])) {
            $loader->load('seo_block.xml');
        }

        $this->registerDoctrineMapping($config);
        $this->registerParameters($container, $config);
    }

    /**
     * @param  \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param  array                                                   $config
     * @return void
     */
    public function registerParameters(ContainerBuilder $container, array $config)
    {
        $container->setParameter('sonata.order.order.class', $config['class']['order']);
        $container->setParameter('sonata.order.order_element.class', $config['class']['order_element']);

        $container->setParameter('sonata.order.admin.order.entity', $config['class']['order']);
        $container->setParameter('sonata.order.admin.order_element.entity', $config['class']['order_element']);
    }

    /**
     * @param  array $config
     * @return void
     */
    public function registerDoctrineMapping(array $config)
    {
        if (!class_exists($config['class']['order'])) {
            return;
        }

        $collector = DoctrineCollector::getInstance();

        $collector->addAssociation($config['class']['order'], 'mapOneToMany', array(
            'fieldName'     => 'orderElements',
            'targetEntity'  => $config['class']['order_element'],
            'cascade'       => array(
                 'persist',
            ),
            'mappedBy'      => 'order',
            'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['order'], 'mapManyToOne', array(
           'fieldName'      => 'customer',
           'targetEntity'   => $config['class']['customer'],
           'cascade'        => array(),
           'mappedBy'       => NULL,
           'inversedBy'     => 'orders',
           'joinColumns'    => array(
                array(
                    'name' => 'customer_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'SET NULL',
                ),
           ),
           'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['order_element'], 'mapManyToOne', array(
            'fieldName'     => 'order',
            'targetEntity'  => $config['class']['order'],
            'cascade'       => array(),
            'mappedBy'      => NULL,
            'inversedBy'    => NULL,
            'joinColumns'   => array(
                array(
                    'name'  => 'order_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ),
            ),
            'orphanRemoval' => false,
        ));

        $collector->addIndex($config['class']['order_element'], 'product_type', array(
            'product_type'
        ));

        $collector->addIndex($config['class']['order_element'], 'order_element_status', array(
            'status'
        ));

        $collector->addIndex($config['class']['order'], 'order_status', array(
            'status'
        ));

        $collector->addIndex($config['class']['order'], 'payment_status', array(
            'payment_status'
        ));
    }
}
