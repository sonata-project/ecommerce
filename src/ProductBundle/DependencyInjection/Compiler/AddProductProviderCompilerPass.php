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

namespace Sonata\ProductBundle\DependencyInjection\Compiler;

use Sonata\Component\Product\ProductDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class AddProductProviderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $pool = $container->getDefinition('sonata.product.pool');

        $calls = $pool->getMethodCalls();
        $pool->setMethodCalls([]);

        $map = [];
        foreach ($calls as $method => $arguments) {
            if ('__hack' !== $arguments[0]) {
                $pool->addMethodCall($arguments[0], $arguments[1]);

                continue;
            }

            foreach ($arguments[1] as $code => $options) {
                // define a new ProductDefinition
                $definition = new Definition(ProductDefinition::class, [new Reference($options['provider']), new Reference($options['manager'])]);
                $definition->setPublic(false);
                $container->setDefinition($code, $definition);

                $container->getDefinition($options['provider'])->addMethodCall('setCode', [$code]);

                $pool->addMethodCall('addProduct', [$code, new Reference($code)]);

                $map[$code] = $container->getDefinition($options['manager'])->getArgument(0);

                $container->getDefinition($options['provider'])->addMethodCall('setBasketElementManager', [new Reference('sonata.basket_element.manager')]);
                $container->getDefinition($options['provider'])->addMethodCall('setCurrencyPriceCalculator', [new Reference('sonata.price.currency.calculator')]);
                $container->getDefinition($options['provider'])->addMethodCall('setProductCategoryManager', [new Reference('sonata.product_category.product')]);
                $container->getDefinition($options['provider'])->addMethodCall('setProductCollectionManager', [new Reference('sonata.product_collection.product')]);
                $container->getDefinition($options['provider'])->addMethodCall('setOrderElementClassName', [$container->getParameter('sonata.order.order_element.class')]);
                $container->getDefinition($options['provider'])->addMethodCall('setEventDispatcher', [new Reference('event_dispatcher')]);

                if (array_key_exists('variations', $options)) {
                    $container->getDefinition($options['provider'])->addMethodCall('setVariationFields', [$options['variations']['fields']]);
                }
            }
        }

        $container->getDefinition('sonata.product.subscriber.orm')->replaceArgument(0, $map);
    }
}
