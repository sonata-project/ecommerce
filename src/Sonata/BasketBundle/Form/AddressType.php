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
            ->add('name', null, array('label' => 'sonata_address_name_label'))
            ->add('firstname', null, array('label' => 'sonata_address_firstname_label'))
            ->add('lastname', null, array('label' => 'sonata_address_lastname_label'))
            ->add('address1', null, array('label' => 'sonata_address_address1_label'))
            ->add('address2', null, array('label' => 'sonata_address_address2_label'))
            ->add('address3', null, array('label' => 'sonata_address_address3_label'))
            ->add('postcode', null, array('label' => 'sonata_address_postcode_label'))
            ->add('city', null, array('label' => 'sonata_address_city_label'))
            ->add('countryCode', null, array('label' => 'sonata_address_countrycode_label'))
            ->add('phone', null, array('label' => 'sonata_address_phone_label'))
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
