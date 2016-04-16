<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle;

use Sonata\ProductBundle\DependencyInjection\Compiler\AddProductProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SonataProductBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddProductProviderCompilerPass());
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $container = $this->container;
        $class     = $this->container->getParameter('sonata.product.product.class');

        call_user_func(array($class, 'setSlugifyMethod'), function ($text) use ($container) {
            $service = $container->get($container->getParameter('sonata.product.slugify_service'));

            return $service->slugify($text);
        });
    }
}
