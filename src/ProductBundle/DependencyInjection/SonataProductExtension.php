<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\DependencyInjection;

use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SonataProductExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
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
        $loader->load('serializer.xml');
        $loader->load('command.xml');

        if (isset($bundles['FOSRestBundle'], $bundles['NelmioApiDocBundle'])) {
            $loader->load('api_controllers.xml');
            $loader->load('api_form.xml');
        }

        if (isset($bundles['SonataAdminBundle'])) {
            $loader->load('admin.xml');
        }

        $pool = $container->getDefinition('sonata.product.pool');
        // this value is altered by the AddProductProviderCompilerPass class
        $pool->addMethodCall('__hack', $config['products']);

        $this->registerParameters($container, $config);
        $this->registerDoctrineMapping($config);
        $this->registerSeoParameters($container, $config);
    }

    public function registerParameters(ContainerBuilder $container, array $config): void
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

    public function registerDoctrineMapping(array $config): void
    {
        if (!class_exists($config['class']['product'])) {
            return;
        }

        $collector = DoctrineCollector::getInstance();

        /*
         * DELIVERY
         */
        $collector->addAssociation($config['class']['delivery'], 'mapManyToOne', [
            'fieldName' => 'product',
            'targetEntity' => $config['class']['product'],
            'cascade' => [
                'persist',
            ],
            'mappedBy' => null,
            'inversedBy' => 'deliveries',
            'joinColumns' => [
                [
                    'name' => 'product_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ],
            ],
            'orphanRemoval' => false,
        ]);

        /*
         * PACKAGE
         */
        $collector->addAssociation($config['class']['package'], 'mapManyToOne', [
            'fieldName' => 'product',
            'targetEntity' => $config['class']['product'],
            'cascade' => [
                'persist',
            ],
            'mappedBy' => null,
            'inversedBy' => 'packages',
            'joinColumns' => [
                [
                    'name' => 'product_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ],
            ],
            'orphanRemoval' => false,
        ]);

        /*
         * PRODUCT CATEGORY
         */
        $collector->addAssociation($config['class']['product_category'], 'mapManyToOne', [
             'fieldName' => 'product',
             'targetEntity' => $config['class']['product'],
             'cascade' => [
                'persist',
             ],
             'mappedBy' => null,
             'inversedBy' => 'productCategories',
             'joinColumns' => [
                 [
                     'name' => 'product_id',
                     'referencedColumnName' => 'id',
                     'onDelete' => 'CASCADE',
                     'onUpdate' => 'CASCADE',
                 ],
             ],
             'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['product_category'], 'mapManyToOne', [
             'fieldName' => 'category',
             'targetEntity' => $config['class']['category'],
             'cascade' => [
                'persist',
             ],
             'mappedBy' => null,
             'joinColumns' => [
                 [
                     'name' => 'category_id',
                     'referencedColumnName' => 'id',
                     'onDelete' => 'CASCADE',
                     'onUpdate' => 'CASCADE',
                 ],
             ],
             'orphanRemoval' => false,
        ]);

        /*
         * PRODUCT COLLECTION
         */
        $collector->addAssociation($config['class']['product_collection'], 'mapManyToOne', [
             'fieldName' => 'product',
             'targetEntity' => $config['class']['product'],
             'cascade' => [
                'persist',
             ],
             'mappedBy' => null,
             'inversedBy' => 'productCollections',
             'joinColumns' => [
                 [
                     'name' => 'product_id',
                     'referencedColumnName' => 'id',
                     'onDelete' => 'CASCADE',
                     'onUpdate' => 'CASCADE',
                 ],
             ],
             'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['product_collection'], 'mapManyToOne', [
             'fieldName' => 'collection',
             'targetEntity' => $config['class']['collection'],
             'cascade' => [
                'persist',
             ],
             'mappedBy' => null,
             'inversedBy' => 'productCollection',
             'joinColumns' => [
                 [
                     'name' => 'collection_id',
                     'referencedColumnName' => 'id',
                     'onDelete' => 'CASCADE',
                     'onUpdate' => 'CASCADE',
                 ],
             ],
             'orphanRemoval' => false,
        ]);

        /*
         * PRODUCT
         */
        $collector->addAssociation($config['class']['product'], 'mapOneToMany', [
            'fieldName' => 'packages',
            'targetEntity' => $config['class']['package'],
            'cascade' => [
               'persist',
            ],
            'mappedBy' => 'product',
            'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['product'], 'mapOneToMany', [
             'fieldName' => 'deliveries',
             'targetEntity' => $config['class']['delivery'],
             'cascade' => [
                 'persist',
             ],
             'mappedBy' => 'product',
             'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['product'], 'mapOneToMany', [
             'fieldName' => 'productCategories',
             'targetEntity' => $config['class']['product_category'],
             'cascade' => [
                'persist',
             ],
             'mappedBy' => 'product',
             'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['product'], 'mapOneToMany', [
             'fieldName' => 'productCollections',
             'targetEntity' => $config['class']['product_collection'],
             'cascade' => [
                'persist',
             ],
             'mappedBy' => 'product',
             'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['product'], 'mapManyToOne', [
            'fieldName' => 'image',
            'targetEntity' => $config['class']['media'],
            'cascade' => [],
            'mappedBy' => null,
            'inversedBy' => null,
            'joinColumns' => [
                [
                    'name' => 'image_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'SET NULL',
                ],
            ],
            'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['product'], 'mapManyToOne', [
             'fieldName' => 'gallery',
             'targetEntity' => $config['class']['gallery'],
             'cascade' => [],
             'mappedBy' => null,
             'inversedBy' => null,
             'joinColumns' => [
                 [
                     'name' => 'gallery_id',
                     'referencedColumnName' => 'id',
                     'onDelete' => 'SET NULL',
                 ],
             ],
             'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['product'], 'mapManyToOne', [
            'fieldName' => 'parent',
            'targetEntity' => $config['class']['product'],
            'cascade' => [
                'persist',
            ],
            'mappedBy' => null,
            'inversedBy' => 'variations',
            'joinColumns' => [
                [
                    'name' => 'parent_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ],
            ],
            'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['product'], 'mapOneToMany', [
            'fieldName' => 'variations',
            'targetEntity' => $config['class']['product'],
            'cascade' => [
                'persist',
            ],
            'mappedBy' => 'parent',
            'orphanRemoval' => false,
        ]);

        $collector->addIndex($config['class']['product'], 'enabled', [
            'enabled',
        ]);
    }

    protected function registerSeoParameters(ContainerBuilder $container, array $config): void
    {
        $productSeo = $config['seo']['product'];

        foreach ($productSeo as $key => $value) {
            $container->setParameter(sprintf('sonata.product.seo.product.%s', $key), $value);
        }
    }
}
