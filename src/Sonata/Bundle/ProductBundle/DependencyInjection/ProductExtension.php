<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\ProductBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * ProductExtension.
 *
 *
 * @author     Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class ProductExtension extends Extension {

    /**
     * Loads the product configuration.
     *
     * @param array            $config    An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function configLoad($config, ContainerBuilder $container) {
        $loader = new XmlFileLoader($container, __DIR__.'/../Resources/config');
        $loader->load('product.xml');

        $definition = new Definition('Sonata\Component\Product\Pool');

        foreach($config['products'] as $product) {

            $definition->addMethodCall('addProduct', array($product));
        }

        $container->setDefinition('sonata.product.pool', $definition);
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
        return 'http://www.sonata-project.org/schema/dic/sonata-product';
    }

    public function getAlias() {
        return "sonata_product";
    }
}