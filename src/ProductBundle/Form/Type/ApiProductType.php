<?php

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

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['provider_name']) {
            $provider = $this->productPool->getProvider($options['provider_name']);
            $provider->buildForm($builder, $options);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,
            'csrf_protection' => false,
            'provider_name' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sonata_product_api_form_product';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        // NEXT_MAJOR: Keep FQCN when bumping Symfony requirement to 2.8+.
        return method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')
            ? 'Sonata\ProductBundle\Form\Type\ApiProductParentType'
            : 'sonata_product_api_form_product_parent';
    }
}
