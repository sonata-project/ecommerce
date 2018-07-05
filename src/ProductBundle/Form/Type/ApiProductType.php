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

namespace Sonata\ProductBundle\Form\Type;

use Sonata\Component\Product\Pool;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class ApiProductType extends AbstractType
{
    /**
     * @var Pool
     */
    protected $productPool;

    /**
     * @param Pool $productPool
     */
    public function __construct(Pool $productPool)
    {
        $this->productPool = $productPool;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['provider_name']) {
            $provider = $this->productPool->getProvider($options['provider_name']);
            $provider->buildForm($builder, $options);
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'csrf_protection' => false,
            'provider_name' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'sonata_product_api_form_product';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getParent()
    {
        return ApiProductParentType::class;
    }
}
