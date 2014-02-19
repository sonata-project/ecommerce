<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\InvoiceBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;

use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;

/**
 *
 * @author     Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SonataInvoiceExtension extends Extension
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
        $loader->load('orm.xml');
        $loader->load('form.xml');
        $loader->load('renderer.xml');

        if (isset($bundles['FOSRestBundle']) && isset($bundles['NelmioApiDocBundle'])) {
            $loader->load('api_controllers.xml');
            $loader->load('serializer.xml');
        }

        if (isset($bundles['SonataAdminBundle'])) {
            $loader->load('admin.xml');
        }

        $this->registerParameters($container, $config);
        $this->registerDoctrineMapping($config);
    }

    /**
     * @param  \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param  array                                                   $config
     * @return void
     */
    public function registerParameters(ContainerBuilder $container, array $config)
    {
        $container->setParameter('sonata.invoice.invoice.class', $config['class']['invoice']);
        $container->setParameter('sonata.invoice.invoice_element.class', $config['class']['invoice_element']);

        $container->setParameter('sonata.invoice.admin.invoice.entity', $config['class']['invoice']);
        $container->setParameter('sonata.invoice.admin.invoice_element.entity', $config['class']['invoice_element']);
    }

    /**
     * @param  array $config
     * @return void
     */
    public function registerDoctrineMapping(array $config)
    {
        if (!class_exists($config['class']['invoice'])) {
            return;
        }

        $collector = DoctrineCollector::getInstance();

        /**
         * INVOICE
         */
        $collector->addAssociation($config['class']['invoice'], 'mapManyToOne', array(
             'fieldName'     => 'customer',
             'targetEntity'  => $config['class']['customer'],
             'cascade'       => array(
                 'persist',
                 'refresh',
                 'merge',
                 'detach',
             ),
             'mappedBy'     => NULL,
             'joinColumns'  => array(
                 array(
                     'name' => 'customer_id',
                     'referencedColumnName' => 'id',
                     'onDelete' => 'SET NULL',
                 ),
             ),
             'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['invoice_element'], 'mapManyToOne', array(
             'fieldName'     => 'invoice',
             'targetEntity'  => $config['class']['invoice'],
             'cascade'       => array(
                 'persist',
                 'refresh',
                 'merge',
                 'detach',
             ),
             'mappedBy'     => NULL,
             'inversedBy'   => 'invoiceElements',
             'joinColumns'  => array(
                 array(
                     'name' => 'invoice_id',
                     'referencedColumnName' => 'id',
                     'onDelete' => 'CASCADE',
                 ),
             ),
             'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['invoice'], 'mapOneToMany', array(
             'fieldName'     => 'invoiceElements',
             'targetEntity'  => $config['class']['invoice_element'],
             'cascade'       => array(
                 'persist',
             ),
             'mappedBy'      => 'invoice',
             'orphanRemoval' => true,
        ));

        $collector->addAssociation($config['class']['invoice_element'], 'mapManyToOne', array(
            'fieldName' => 'orderElement',
            'targetEntity' => $config['class']['order_element'],
            'cascade' => array(),
            'joinColumns'  => array(
                 array(
                     'name' => 'order_element_id',
                     'referencedColumnName' => 'id',
                     'onDelete' => 'CASCADE',
                 ),
             ),
        ));
    }
}
