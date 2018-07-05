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

namespace Sonata\CustomerBundle;

use Sonata\CoreBundle\Form\FormHelper;
use Sonata\CustomerBundle\Form\Type\AddressType;
use Sonata\CustomerBundle\Form\Type\AddressTypeType;
use Sonata\CustomerBundle\Form\Type\ApiAddressType;
use Sonata\CustomerBundle\Form\Type\ApiCustomerType;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SonataCustomerBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $this->registerFormMapping();
    }

    public function boot(): void
    {
        $this->registerFormMapping();
    }

    /**
     * Register form mapping information.
     */
    public function registerFormMapping(): void
    {
        FormHelper::registerFormTypeMapping([
            'sonata_customer_address' => AddressType::class,
            'sonata_customer_address_types' => AddressTypeType::class,
            'sonata_customer_api_form_customer' => ApiCustomerType::class,
            'sonata_customer_api_form_address' => ApiAddressType::class,
        ]);
    }
}
