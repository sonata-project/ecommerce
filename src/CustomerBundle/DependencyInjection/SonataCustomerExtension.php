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

namespace Sonata\CustomerBundle\DependencyInjection;

use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SonataCustomerExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        if ($container->hasExtension('twig')) {
            // add custom form widgets
            $container->prependExtensionConfig('twig', ['form_themes' => ['@SonataCore/Form/datepicker.html.twig']]);
        }
    }
    /**
     * Loads the customer configuration.
     *
     * @param array            $configs   An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
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

        if (isset($bundles['FOSRestBundle'], $bundles['NelmioApiDocBundle'])) {
            $loader->load('api_controllers.xml');
            $loader->load('api_form.xml');
            $loader->load('serializer.xml');
        }

        if (isset($bundles['SonataAdminBundle'])) {
            $loader->load('admin.xml');
        }

        $this->registerDoctrineMapping($config);
        $this->registerParameters($container, $config);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array                                                   $config
     */
    public function registerParameters(ContainerBuilder $container, array $config): void
    {
        $container->setParameter('sonata.customer.customer.class', $config['class']['customer']);
        $container->setParameter('sonata.customer.address.class', $config['class']['address']);
        $container->setParameter('sonata.customer.selector.class', $config['class']['customer_selector']);

        $container->setParameter('sonata.customer.admin.customer.entity', $config['class']['customer']);
        $container->setParameter('sonata.customer.admin.address.entity', $config['class']['address']);
    }

    /**
     * @param array $config
     */
    public function registerDoctrineMapping(array $config): void
    {
        if (!class_exists($config['class']['customer'])) {
            return;
        }

        $collector = DoctrineCollector::getInstance();

        $collector->addAssociation($config['class']['customer'], 'mapOneToMany', [
            'fieldName' => 'addresses',
            'targetEntity' => $config['class']['address'],
            'cascade' => [
                'persist',
            ],
            'mappedBy' => 'customer',
            'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['customer'], 'mapOneToMany', [
            'fieldName' => 'orders',
            'targetEntity' => $config['class']['order'],
            'cascade' => [
                'persist',
            ],
            'mappedBy' => 'customer',
            'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['customer'], 'mapManyToOne', [
            'fieldName' => 'user',
            'targetEntity' => $config['class']['user'],
            'cascade' => [
                'persist',
            ],
            'mappedBy' => null,
            'inversedBy' => 'customers',
            'joinColumns' => [
                [
                    'name' => 'user_id',
                    'referencedColumnName' => $config['field']['customer']['user'],
                    'onDelete' => 'SET NULL',
                ],
            ],
            'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['address'], 'mapManyToOne', [
            'fieldName' => 'customer',
            'targetEntity' => $config['class']['customer'],
            'cascade' => [
                'persist',
            ],
            'mappedBy' => null,
            'inversedBy' => 'addresses',
            'joinColumns' => [
                [
                    'name' => 'customer_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ],
            ],
            'orphanRemoval' => false,
        ]);
    }
}
