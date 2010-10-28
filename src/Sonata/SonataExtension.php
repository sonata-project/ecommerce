<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 *  ugly hack to allow a global 'sonata' configuration key in the config.format file ...
 *
 */
abstract class SonataExtension extends Extension
{

    public function deliveryLoad(array $config, ContainerBuilder $configuration)
    {

    }


    public function basketLoad(array $config, ContainerBuilder $configuration)
    {

    }

    public function paymentLoad(array $config, ContainerBuilder $configuration)
    {

    }

    public function getAlias()
    {
        return 'sonata';
    }
}