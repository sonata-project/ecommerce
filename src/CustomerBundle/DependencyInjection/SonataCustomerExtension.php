<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\CustomerBundle\DependencyInjection;

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
class SonataCustomerExtension extends Extension
{
    /**
     * Loads the customer configuration.
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
            $loader->load('api_form.xml');
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
        $container->setParameter('sonata.customer.customer.class', $config['class']['customer']);
        $container->setParameter('sonata.customer.address.class', $config['class']['address']);
        $container->setParameter('sonata.customer.selector.class', $config['class']['customer_selector']);

        $container->setParameter('sonata.customer.admin.customer.entity', $config['class']['customer']);
        $container->setParameter('sonata.customer.admin.address.entity', $config['class']['address']);
    }

    /**
     * @param  array $config
     * @return void
     */
    public function registerDoctrineMapping(array $config)
    {
        if (!class_exists($config['class']['customer'])) {
            return;
        }

        $collector = DoctrineCollector::getInstance();

        $collector->addAssociation($config['class']['customer'], 'mapOneToMany', array(
            'fieldName'    => 'addresses',
            'targetEntity' => $config['class']['address'],
            'cascade'      => array(
                'persist',
            ),
            'mappedBy'      => 'customer',
            'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['customer'], 'mapOneToMany', array(
            'fieldName'     => 'orders',
            'targetEntity'  => $config['class']['order'],
            'cascade'       => array(
                'persist',
            ),
            'mappedBy' => 'customer',
            'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['customer'], 'mapManyToOne', array(
            'fieldName'    => 'user',
            'targetEntity' => $config['class']['user'],
            'cascade'      => array(
                'persist',
            ),
            'mappedBy'     => NULL,
            'inversedBy'   => 'customers',
            'joinColumns'  => array(
                array(
                    'name' => 'user_id',
                    'referencedColumnName' => $config['field']['customer']['user'],
                    'onDelete' => 'SET NULL',
                ),
            ),
            'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['address'], 'mapManyToOne', array(
            'fieldName'    => 'customer',
            'targetEntity' => $config['class']['customer'],
            'cascade'      => array(
                'persist',
            ),
            'mappedBy'     => NULL,
            'inversedBy'   => 'addresses',
            'joinColumns'  => array(
                array(
                    'name' => 'customer_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ),
            ),
            'orphanRemoval' => false,
        ));
    }
}
