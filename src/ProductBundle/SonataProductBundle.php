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

namespace Sonata\ProductBundle;

use Sonata\CoreBundle\Form\FormHelper;
use Sonata\ProductBundle\DependencyInjection\Compiler\AddProductProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SonataProductBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new AddProductProviderCompilerPass());

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
            'sonata_product_delivery_status' => 'Sonata\ProductBundle\Form\Type\ProductDeliveryStatusType',
            'sonata_product_variation_choices' => 'Sonata\Component\Form\Type\VariationChoiceType',
            'sonata_product_api_form_product_parent' => 'Sonata\ProductBundle\Form\Type\ApiProductParentType',
            'sonata_product_api_form_product' => 'Sonata\ProductBundle\Form\Type\ApiProductType',
            'sonata_currency' => 'Sonata\Component\Currency\CurrencyFormType',
        ]);
    }
}
