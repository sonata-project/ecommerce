<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 *
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class AddProductProviderCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $pool = $container->getDefinition('sonata.product.pool');

        $calls = $pool->getMethodCalls();
        $pool->setMethodCalls(array());

        $map = array();
        foreach ($calls as $method => $arguments) {
            if ($arguments[0] !== '__hack') {
                $pool->addMethodCall($arguments[0], $arguments[1]);
                continue;
            }

            foreach ($arguments[1] as $code => $options) {
                // define a new ProductDefinition
                $definition = new Definition('Sonata\Component\Product\ProductDefinition', array(new Reference($options['provider']), new Reference($options['manager'])));
                $definition->setPublic(false);
                $container->setDefinition($code, $definition);

                $container->getDefinition($options['provider'])->addMethodCall('setCode', array($code));

                $pool->addMethodCall('addProduct', array($code, new Reference($code)));

                $map[$code] = $container->getDefinition($options['manager'])->getArgument(0);

                $container->getDefinition($options['provider'])->addMethodCall('setBasketElementManager', array(new Reference('sonata.basket_element.manager')));
            }
        }

        $container->getDefinition('sonata.product.subscriber.orm')->replaceArgument(0, $map);
    }
}
