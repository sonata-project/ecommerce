<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Sonata\ProductBundle\DependencyInjection\Compiler\AddProductProviderCompilerPass;

class SonataProductBundle extends Bundle
{
    /**
     * @param  \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @return void
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddProductProviderCompilerPass());
    }

    /**
     * Boots the Bundle.
     */
    public function boot()
    {
    }
}
