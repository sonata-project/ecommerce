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
use Symfony\Component\Form\FormInterface;
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
        $addresses = $options['addresses'];

        if (count($addresses) > 0) {
            $builder->add('addresses', 'choice', array(
                    'choices'  => $addresses,
                    'expanded' => false,
                    'multiple' => false,
                    'mapped'   => false,
                ))
                ->add('useSelected', 'submit', array(
                        'attr'               => array(
                            'class' => 'btn btn-primary',
                            'style' => 'margin-bottom:20px;'
                        ),
                        'label'              => 'sonata_basket_address_useselected',
                        'translation_domain' => 'SonataBasketBundle',
                        'validation_groups'  => false
                    )
                );
        }

        $builder
            ->add('name', null, array('required' => !count($addresses)))
            ->add('firstname', null, array('required' => !count($addresses)))
            ->add('lastname', null, array('required' => !count($addresses)))
            ->add('address1', null, array('required' => !count($addresses)))
            ->add('address2')
            ->add('address3')
            ->add('postcode', null, array('required' => !count($addresses)))
            ->add('city', null, array('required' => !count($addresses)))
            ->add('countryCode', 'country', array('required' => !count($addresses)))
            ->add('phone')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->addressClass,
            'addresses'  => array(),
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
