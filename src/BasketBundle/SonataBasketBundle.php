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
use Sonata\CoreBundle\Form\FormHelper;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SonataBasketBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new GlobalVariableCompilerPass());

        $this->registerFormMapping();
    }

    /**
     * {@inheritdoc}
     */
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
            'sonata_basket_basket' => 'Sonata\BasketBundle\Form\BasketType',
            'sonata_basker_address' => 'Sonata\BasketBundle\Form\Type\AddressType',
            'sonata_basket_shipping' => 'Sonata\BasketBundle\Form\ShippingType',
            'sonata_basket_payment' => 'Sonata\BasketBundle\Form\PaymentType',
            'sonata_basket_api_form_basket' => 'Sonata\BasketBundle\Form\ApiBasketType',
            'sonata_basket_api_form_basket_element' => 'Sonata\BasketBundle\Form\ApiBasketElementType',
            'sonata_basket_api_form_basket_parent' => 'Sonata\BasketBundle\Form\ApiBasketParentType',
            'sonata_basket_api_form_basket_element_parent' => 'Sonata\BasketBundle\Form\ApiBasketElementParentType',
        ]);
    }
}
