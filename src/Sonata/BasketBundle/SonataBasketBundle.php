<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\BasketBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Sonata\BasketBundle\DependencyInjection\Compiler\GlobalVariableCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SonataBasketBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new GlobalVariableCompilerPass);
    }
}
