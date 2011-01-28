<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\BasketBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * BasketExtension.
 *
 *
 * @author     Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class BasketExtension extends Extension
{

    /**
     * Loads the url shortener configuration.
     *
     * @param array            $config    An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function configLoad($configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, __DIR__.'/../Resources/config');
        $loader->load('basket.xml');

        foreach($configs as $config) {
            // define the basket loader service
            $definition = new Definition('Sonata\\Component\\Basket\\Loader');
            $definition
                ->addArgument($config['class'])
                ->addMethodCall('setSession',       array(new Reference('session')))   // store the basket into session
                ->addMethodCall('setProductPool',   array(new Reference('sonata.product.pool')))
                ->addMethodCall('setEntityManager', array(new Reference('doctrine.orm.default_entity_manager')))
                ->addMethodCall('setPaymentPool',   array(new Reference('sonata.payment.pool')))
                ->addMethodCall('setDeliveryPool',  array(new Reference('sonata.delivery.pool')))
            ;

            $container->setDefinition('sonata.basket.loader', $definition);

            // define the basket service which depends on the basket loader (load the basket from the session)
            $definition = new Definition($config['class']);
            $definition
//                ->setSynthetic(true)
                ->setFactoryService('sonata.basket.loader')
                ->setFactoryMethod('getBasket')
            ;

            $container->setDefinition('sonata.basket', $definition);


            // initialize the basket elements validator
            $definition = new Definition('Sonata\\Component\\Form\\BasketElementCollectionValidator');
            $definition
                ->addMethodCall('setProductPool', array(new Reference('sonata.product.pool')))
                ->addMethodCall('setBasket', array(new Reference('sonata.basket')))
                ->addTag('validator.constraint_validator', array('alias' => 'sonata_basket_element_collection_validator'))
             ;

            $container->setDefinition('sonata.basket.elements.validator', $definition);
        }

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

        return 'http://www.sonata-project.org/schema/dic/sonata-basket';
    }

    public function getAlias()
    {
        
        return "sonata_basket";
    }
}