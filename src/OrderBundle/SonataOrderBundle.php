<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\OrderBundle;

use Sonata\CoreBundle\Form\FormHelper;
use Sonata\OrderBundle\Form\Type\OrderStatusType;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SonataOrderBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $this->registerFormMapping();
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->registerFormMapping();
    }

    /**
     * Register form mapping information.
     */
    public function registerFormMapping()
    {
        FormHelper::registerFormTypeMapping([
            'sonata_order_status' => OrderStatusType::class,
        ]);
    }
}
