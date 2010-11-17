<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\BasketBundle\DependencyInjection;

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
class BasketExtension extends Extension {

    /**
     * Loads the url shortener configuration.
     *
     * @param array            $config    An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function configLoad($config, ContainerBuilder $container) {
        $loader = new XmlFileLoader($container, __DIR__.'/../Resources/config');
        $loader->load('basket.xml');

        $definition = new Definition();
        $definition
            ->setFactoryService('sonata.basket.loader')
            ->setFactoryMethod('getBasket')
        ;

        $container->setDefinition('sonata.basket', $definition);

        $definition = new Definition('Sonata\\Component\\Basket\\Loader');
        $definition
            ->addArgument(array($config['class']))
            ->addMethodCall('setSession',       array(new Reference('session')))   // store the basket into session
            ->addMethodCall('setProductPool',   array(new Reference('sonata.product.pool')))
        ;

        $container->setDefinition('sonata.basket.loader', $definition);
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

        return 'http://www.sonata-project.org/schema/dic/sonata-basket';
    }

    public function getAlias() {
        
        return "sonata_basket";
    }
}