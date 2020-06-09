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

namespace Sonata\OrderBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class StatusRendererCompilerPass implements CompilerPassInterface
{
    /**
     * {@innheritdoc}.
     */
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds('sonata.status.renderer') as $id => $attributes) {
            $container->getDefinition('sonata.order.twig.status_extension')->addMethodCall('addStatusService', [new Reference($id)]);
        }
    }
}
