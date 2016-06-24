<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\CustomerBundle;

use Sonata\CoreBundle\Form\FormHelper;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SonataCustomerBundle extends Bundle
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
        FormHelper::registerFormTypeMapping(array(
            'sonata_customer_address' => 'Sonata\CustomerBundle\Form\Type\AddressType',
            'sonata_customer_address_types' => 'Sonata\CustomerBundle\Form\Type\AddressTypeType',
            'sonata_customer_api_form_customer' => 'Sonata\CoreBundle\Form\Type\DoctrineORMSerializationType',
            'sonata_customer_api_form_address' => 'Sonata\CoreBundle\Form\Type\DoctrineORMSerializationType',
        ));
    }
}
