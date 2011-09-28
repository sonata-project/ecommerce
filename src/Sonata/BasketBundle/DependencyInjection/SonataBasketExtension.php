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
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;

/**
 * BasketExtension.
 *
 *
 * @author     Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SonataBasketExtension extends Extension
{
    /**
     * Loads the url shortener configuration.
     *
     * @param array            $config    An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = call_user_func_array('array_merge_recursive', $config);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('basket.xml');
        $loader->load('validator.xml');
        $loader->load('form.xml');
        $loader->load('twig.xml');

        $basketLoader = isset($config['loader']) ? $config['loader'] : 'sonata.basket.loader.standard';

        $container->setAlias('sonata.basket.loader', $basketLoader);
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