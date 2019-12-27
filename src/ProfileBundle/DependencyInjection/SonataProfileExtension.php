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

namespace Sonata\ProfileBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class SonataProfileExtension extends Extension
{
    /**
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

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('block.xml');
        $loader->load('menu.xml');

        $this->configureProfile($config, $container);
        $this->configureMenu($config, $container);

        $loader->load('twig.xml');
        $this->configureTemplate($config, $container);
    }

    private function configureProfile(array $config, ContainerBuilder $container)
    {
        $container->setParameter('sonata.profile.configuration.profile_blocks', $config['dashboard']['blocks']);
    }

    private function configureTemplate(array $config, ContainerBuilder $container)
    {
        $container->setParameter('sonata.profile.template', $config['template']);
    }

    private function configureMenu(array $config, ContainerBuilder $container)
    {
        $container->getDefinition('sonata.profile.profile.menu_builder')->replaceArgument(2, $config['menu']);
    }
}
