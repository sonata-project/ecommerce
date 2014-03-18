<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;

use Symfony\Component\Config\Definition\Processor;
use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;

/**
 * ProductExtension.
 *
 *
 * @author     Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SonataProductExtension extends Extension
{
    /**
     * Loads the product configuration.
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
        $loader->load('product.xml');
        $loader->load('orm.xml');
        $loader->load('form.xml');
        $loader->load('twig.xml');
        $loader->load('menu.xml');

        if (isset($bundles['FOSRestBundle']) && isset($bundles['NelmioApiDocBundle'])) {
            $loader->load('api_controllers.xml');
            $loader->load('api_form.xml');
            $loader->load('serializer.xml');
        }

        if (isset($bundles['SonataAdminBundle'])) {
            $loader->load('admin.xml');
        }

        $pool = $container->getDefinition('sonata.product.pool');
        // this value is altered by the AddProductProviderPass class
        $pool->addMethodCall('__hack', $config['products']);

        $this->registerParameters($container, $config);
        $this->registerDoctrineMapping($config);
        $this->registerSeoParameters($container, $config);
    }

    /**
     * @param  \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param  array                                                   $config
     * @return void
     */
    public function registerParameters(ContainerBuilder $container, array $config)
    {
        $container->setParameter('sonata.product.product.class', $config['class']['product']);
        $container->setParameter('sonata.product.package.class', $config['class']['package']);
        $container->setParameter('sonata.product.product_category.class', $config['class']['product_category']);
        $container->setParameter('sonata.product.product_collection.class', $config['class']['product_collection']);
        $container->setParameter('sonata.product.category.class', $config['class']['category']);
        $container->setParameter('sonata.product.collection.class', $config['class']['collection']);
        $container->setParameter('sonata.product.delivery.class', $config['class']['delivery']);

        $container->setParameter('sonata.product.admin.product.entity', $config['class']['product']);
        $container->setParameter('sonata.product.admin.package.entity', $config['class']['package']);
        $container->setParameter('sonata.product.admin.product_category.entity', $config['class']['product_category']);
        $container->setParameter('sonata.product.admin.product_collection.entity', $config['class']['product_collection']);
        $container->setParameter('sonata.product.admin.category.entity', $config['class']['category']);
        $container->setParameter('sonata.product.admin.collection.entity', $config['class']['collection']);
        $container->setParameter('sonata.product.admin.delivery.entity', $config['class']['delivery']);
    }

    /**
     * @param  array $config
     * @return void
     */
    public function registerDoctrineMapping(array $config)
    {
        if (!class_exists($config['class']['product'])) {
            return;
        }

        $collector = DoctrineCollector::getInstance();

        /**
         * DELIVERY
         */
        $collector->addAssociation($config['class']['delivery'], 'mapManyToOne', array(
            'fieldName'    => 'product',
            'targetEntity' => $config['class']['product'],
            'cascade'      => array(
                'persist'
            ),
            'mappedBy'     => NULL,
            'inversedBy'   => 'deliveries',
            'joinColumns'  => array(
                array(
                    'name' => 'product_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ),
            ),
            'orphanRemoval' => false,
        ));

        /**
         * PACKAGE
         */
        $collector->addAssociation($config['class']['package'], 'mapManyToOne', array(
            'fieldName'    => 'product',
            'targetEntity' => $config['class']['product'],
            'cascade'      => array(
                'persist'
            ),
            'mappedBy'     => NULL,
            'inversedBy'   => 'packages',
            'joinColumns'  => array(
                array(
                    'name' => 'product_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ),
            ),
            'orphanRemoval' => false,
        ));

        /**
         * PRODUCT CATEGORY
         */
        $collector->addAssociation($config['class']['product_category'], 'mapManyToOne', array(
             'fieldName'    => 'product',
             'targetEntity' => $config['class']['product'],
             'cascade'      => array(
                'persist',
             ),
             'mappedBy'     => NULL,
             'inversedBy'   => 'productCategories',
             'joinColumns'  => array(
                 array(
                     'name' => 'product_id',
                     'referencedColumnName' => 'id',
                     'onDelete' => 'CASCADE',
                     'onUpdate' => 'CASCADE',
                 ),
             ),
             'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['product_category'], 'mapManyToOne', array(
             'fieldName'    => 'category',
             'targetEntity' => $config['class']['category'],
             'cascade'      => array(
                'persist',
             ),
             'mappedBy'     => NULL,
             'joinColumns'  => array(
                 array(
                     'name' => 'category_id',
                     'referencedColumnName' => 'id',
                     'onDelete' => 'CASCADE',
                     'onUpdate' => 'CASCADE',
                 ),
             ),
             'orphanRemoval' => false,
        ));

        /**
         * PRODUCT COLLECTION
         */
        $collector->addAssociation($config['class']['product_collection'], 'mapManyToOne', array(
             'fieldName'    => 'product',
             'targetEntity' => $config['class']['product'],
             'cascade'      => array(
                'persist',
             ),
             'mappedBy'     => NULL,
             'inversedBy'   => 'productCollections',
             'joinColumns'  => array(
                 array(
                     'name' => 'product_id',
                     'referencedColumnName' => 'id',
                     'onDelete' => 'CASCADE',
                     'onUpdate' => 'CASCADE',
                 ),
             ),
             'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['product_collection'], 'mapManyToOne', array(
             'fieldName'    => 'collection',
             'targetEntity' => $config['class']['collection'],
             'cascade'      => array(
                'persist',
             ),
             'mappedBy'     => NULL,
             'inversedBy'   => 'productCollection',
             'joinColumns'  => array(
                 array(
                     'name' => 'collection_id',
                     'referencedColumnName' => 'id',
                     'onDelete' => 'CASCADE',
                     'onUpdate' => 'CASCADE',
                 ),
             ),
             'orphanRemoval' => false,
        ));

        /**
         * PRODUCT
         */
        $collector->addAssociation($config['class']['product'], 'mapOneToMany', array(
            'fieldName'     => 'packages',
            'targetEntity'  => $config['class']['package'],
            'cascade'       => array(
               'persist'
            ),
            'mappedBy'      => 'product',
            'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['product'], 'mapOneToMany', array(
             'fieldName'     => 'deliveries',
             'targetEntity'  => $config['class']['delivery'],
             'cascade'       => array(
                 'persist'
             ),
             'mappedBy'      => 'product',
             'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['product'], 'mapOneToMany', array(
             'fieldName'     => 'productCategories',
             'targetEntity'  => $config['class']['product_category'],
             'cascade'       => array(
                'persist'
             ),
             'mappedBy'      => 'product',
             'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['product'], 'mapOneToMany', array(
             'fieldName'     => 'productCollections',
             'targetEntity'  => $config['class']['product_collection'],
             'cascade'       => array(
                'persist'
             ),
             'mappedBy'      => 'product',
             'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['product'], 'mapManyToOne', array(
            'fieldName'     => 'image',
            'targetEntity'  => $config['class']['media'],
            'cascade'       => array(),
            'mappedBy'      => NULL,
            'inversedBy'    => NULL,
            'joinColumns'   => array(
                array(
                    'name' => 'image_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'SET NULL',
                ),
            ),
            'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['product'], 'mapManyToOne', array(
             'fieldName'     => 'gallery',
             'targetEntity'  => $config['class']['gallery'],
             'cascade'       => array(),
             'mappedBy'      => NULL,
             'inversedBy'    => NULL,
             'joinColumns'   => array(
                 array(
                     'name' => 'gallery_id',
                     'referencedColumnName' => 'id',
                     'onDelete' => 'SET NULL',
                 ),
             ),
             'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['product'], 'mapManyToOne', array(
            'fieldName'    => 'parent',
            'targetEntity' => $config['class']['product'],
            'cascade'      => array(
                'persist'
            ),
            'mappedBy'     => NULL,
            'inversedBy'   => 'variations',
            'joinColumns'  => array(
                array(
                    'name' => 'parent_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ),
            ),
            'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['product'], 'mapOneToMany', array(
            'fieldName'     => 'variations',
            'targetEntity'  => $config['class']['product'],
            'cascade'       => array(
                'persist'
            ),
            'mappedBy'      => 'parent',
            'orphanRemoval' => false,
        ));

        $collector->addIndex($config['class']['product'], 'enabled', array(
            'enabled'
        ));
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    protected function registerSeoParameters(ContainerBuilder $container, array $config)
    {
        $productSeo = $config['seo']['product'];

        foreach ($productSeo as $key => $value) {
            $container->setParameter(sprintf('sonata.product.seo.product.%s', $key), $value);
        }
    }
}
