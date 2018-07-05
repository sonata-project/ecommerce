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

namespace Sonata\BasketBundle;

use Sonata\BasketBundle\DependencyInjection\Compiler\GlobalVariableCompilerPass;
use Sonata\BasketBundle\Form\AddressType;
use Sonata\BasketBundle\Form\ApiBasketElementParentType;
use Sonata\BasketBundle\Form\ApiBasketElementType;
use Sonata\BasketBundle\Form\ApiBasketParentType;
use Sonata\BasketBundle\Form\ApiBasketType;
use Sonata\BasketBundle\Form\BasketType;
use Sonata\BasketBundle\Form\PaymentType;
use Sonata\BasketBundle\Form\ShippingType;
use Sonata\CoreBundle\Form\FormHelper;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SonataBasketBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new GlobalVariableCompilerPass());

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
            'sonata_basket_basket' => BasketType::class,
            'sonata_basket_address' => AddressType::class,
            'sonata_basket_shipping' => ShippingType::class,
            'sonata_basket_payment' => PaymentType::class,
            'sonata_basket_api_form_basket' => ApiBasketType::class,
            'sonata_basket_api_form_basket_element' => ApiBasketElementType::class,
            'sonata_basket_api_form_basket_parent' => ApiBasketParentType::class,
            'sonata_basket_api_form_basket_element_parent' => ApiBasketElementParentType::class,
        ]);
    }
}
