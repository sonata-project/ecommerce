<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\ProductBundle\Form\Type;

use Metadata\MetadataFactoryInterface;

use Sonata\Component\Product\Pool;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ApiProductType
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class ApiProductType extends AbstractType
{
    /**
     * @var Pool $productPool
     */
    protected $productPool;

    /**
     * Constructor
     *
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
            'data_class'      => null,
            'csrf_protection' => false,
            'provider_name'   => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata_product_api_form_product';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'sonata_product_api_form_product_parent';
    }
}