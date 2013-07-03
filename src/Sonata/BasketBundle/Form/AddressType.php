<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\BasketBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 *
 * Adress form type (used for deliveryAddressStep in Order process)
 */
class AddressType extends AbstractType
{
    protected $addressClass;

    /**
     * @param string $addressClass
     */
    public function __construct($addressClass)
    {
        $this->addressClass = $addressClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('firstname')
            ->add('lastname')
            ->add('address1')
            ->add('address2')
            ->add('address3')
            ->add('postcode')
            ->add('city')
            ->add('countryCode')
            ->add('phone')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->addressClass
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata_basket_address';
    }
}
