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

use Sonata\Component\Currency\CurrencyFormType;
use Sonata\Component\Form\Type\VariationChoiceType;
use Sonata\CoreBundle\Form\FormHelper;
use Sonata\ProductBundle\DependencyInjection\Compiler\AddProductProviderCompilerPass;
use Sonata\ProductBundle\Form\Type\ApiProductParentType;
use Sonata\ProductBundle\Form\Type\ApiProductType;
use Sonata\ProductBundle\Form\Type\ProductDeliveryStatusType;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SonataProductBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new AddProductProviderCompilerPass());

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
            'sonata_product_delivery_status' => ProductDeliveryStatusType::class,
            'sonata_product_variation_choices' => VariationChoiceType::class,
            'sonata_product_api_form_product_parent' => ApiProductParentType::class,
            'sonata_product_api_form_product' => ApiProductType::class,
            'sonata_currency' => CurrencyFormType::class,
        ]);
    }
}
