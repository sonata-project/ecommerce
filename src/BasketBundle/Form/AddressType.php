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

namespace Sonata\BasketBundle\Form;

use Sonata\Component\Basket\BasketInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 *
 * Address form type (used for deliveryAddressStep in Order process)
 */
class AddressType extends AbstractType
{
    /**
     * @var string
     */
    protected $addressClass;

    /**
     * @var BasketInterface
     */
    protected $basket;

    /**
     * @param string          $addressClass An address entity class name
     * @param BasketInterface $basket       Sonata current basket
     */
    public function __construct($addressClass, BasketInterface $basket)
    {
        $this->addressClass = $addressClass;
        $this->basket = $basket;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $addresses = $options['addresses'];

        if (count($addresses) > 0) {
            $defaultAddress = current($addresses);

            foreach ($addresses as $address) {
                if ($address->getCurrent()) {
                    $defaultAddress = $address;

                    break;
                }
            }

            $builder->add('addresses', EntityType::class, [
                'choices' => $addresses,
                'preferred_choices' => [$defaultAddress],
                'class' => $this->addressClass,
                'expanded' => true,
                'multiple' => false,
                'mapped' => false,
            ])
            ->add('useSelected', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary',
                    'style' => 'margin-bottom:20px;',
                ],
                'translation_domain' => 'SonataBasketBundle',
                'validation_groups' => false,
            ]);
        }

        $builder->add('name', null, ['required' => !count($addresses)]);

        if (isset($options['types'])) {
            $typeOptions = [
                'choices' => array_flip($options['types']),
                'translation_domain' => 'SonataCustomerBundle',
            ];

            // choice_as_value option is not needed in SF 3.0+
            if (method_exists(FormTypeInterface::class, 'setDefaultOptions')) {
                $typeOptions['choices_as_values'] = true;
            }

            $builder->add('type', ChoiceType::class, $typeOptions);
        }

        $builder
            ->add('firstname', null, ['required' => !count($addresses)])
            ->add('lastname', null, ['required' => !count($addresses)])
            ->add('address1', null, ['required' => !count($addresses)])
            ->add('address2')
            ->add('address3')
            ->add('postcode', null, ['required' => !count($addresses)])
            ->add('city', null, ['required' => !count($addresses)])
            ->add('phone')
        ;

        $countries = $this->getBasketDeliveryCountries();

        $countryOptions = ['required' => !count($addresses)];

        if (count($countries) > 0) {
            // choice_as_value options is not needed in SF 3.0+
            if (method_exists(FormTypeInterface::class, 'setDefaultOptions')) {
                $countryOptions['choices_as_values'] = true;
            }

            $countryOptions['choices'] = array_flip($countries);
        }

        $builder->add('countryCode', CountryType::class, $countryOptions);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['addresses'] = $options['addresses'];
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => $this->addressClass,
            'addresses' => [],
            'validation_groups' => ['front'],
        ]);
    }

    public function getBlockPrefix()
    {
        return 'sonata_basket_address';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * Returns basket elements delivery countries.
     *
     * @return array
     */
    protected function getBasketDeliveryCountries()
    {
        $countries = [];

        foreach ($this->basket->getBasketElements() as $basketElement) {
            $product = $basketElement->getProduct();

            foreach ($product->getDeliveries() as $delivery) {
                $code = $delivery->getCountryCode();

                if (!isset($countries[$code])) {
                    $countries[$code] = Intl::getRegionBundle()->getCountryName($code);
                }
            }
        }

        return $countries;
    }
}
