<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\BasketBundle\DependencyInjection;

use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * BasketExtension.
 *
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SonataBasketExtension extends Extension
{
    /**
     * Loads the url shortener configuration.
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
        $loader->load('basket_entity.xml');
        $loader->load('basket_session.xml');
        $loader->load('basket.xml');
        $loader->load('validator.xml');
        $loader->load('form.xml');
        $loader->load('twig.xml');

        if (isset($bundles['FOSRestBundle']) && isset($bundles['NelmioApiDocBundle'])) {
            $loader->load('api_controllers.xml');
            $loader->load('api_form.xml');
        }

        $container->setAlias('sonata.basket.builder', $config['builder']);
        $container->setAlias('sonata.basket.factory', $config['factory']);
        $container->setAlias('sonata.basket.loader', $config['loader']);

        // NEXT_MAJOR: Remove following lines.
        $basketDefinition = $container->getDefinition('sonata.basket');
        if (method_exists($basketDefinition, 'setFactory')) {
            $basketDefinition->setFactory(array(new Reference('sonata.basket.loader'), 'getBasket'));
        } else {
            $basketDefinition->setFactoryClass(new Reference('sonata.basket.loader'));
            $basketDefinition->setFactoryMethod('getBasket');
        }

        // Set the SecurityContext for Symfony <2.6
        // NEXT_MAJOR: Go back to simple xml configuration when bumping requirements to SF 2.6+
        if (interface_exists('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface')) {
            $tokenStorageReference = new Reference('security.token_storage');
            $authorizationCheckerReference = new Reference('security.authorization_checker');
        } else {
            $tokenStorageReference = new Reference('security.context');
            $authorizationCheckerReference = new Reference('security.context');
        }

        $container
            ->getDefinition('sonata.customer.selector')
            ->replaceArgument(2, $tokenStorageReference)
        ;

        $container
            ->getDefinition('sonata.customer.selector')
            ->replaceArgument(3, $authorizationCheckerReference)
        ;

        $this->registerParameters($container, $config);
        $this->registerDoctrineMapping($config);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param $config
     */
    public function registerParameters(ContainerBuilder $container, array $config)
    {
        $container->setParameter('sonata.basket.basket.class', $config['class']['basket']);
        $container->setParameter('sonata.basket.basket_element.class', $config['class']['basket_element']);
    }

    /**
     * @param array $config
     */
    public function registerDoctrineMapping(array $config)
    {
        if (!class_exists($config['class']['basket'])) {
            return;
        }

        $collector = DoctrineCollector::getInstance();

        $collector->addAssociation($config['class']['basket'], 'mapManyToOne', array(
             'fieldName' => 'customer',
             'targetEntity' => $config['class']['customer'],
             'cascade' => array(),
             'mappedBy' => null,
             'inversedBy' => null,
             'joinColumns' => array(
                 array(
                     'name' => 'customer_id',
                     'referencedColumnName' => 'id',
                     'onDelete' => 'CASCADE',
                     'unique' => true,
                 ),
             ),
             'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['basket'], 'mapOneToMany', array(
             'fieldName' => 'basketElements',
             'targetEntity' => $config['class']['basket_element'],
             'cascade' => array(
                 'persist',
             ),
             'mappedBy' => 'basket',
             'orphanRemoval' => true,
        ));

        $collector->addAssociation($config['class']['basket_element'], 'mapManyToOne', array(
            'fieldName' => 'basket',
            'targetEntity' => $config['class']['basket'],
            'cascade' => array(),
            'mappedBy' => null,
            'inversedBy' => 'basketElements',
            'joinColumns' => array(
                array(
                    'name' => 'basket_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ),
            ),
            'orphanRemoval' => false,
        ));
    }
}
