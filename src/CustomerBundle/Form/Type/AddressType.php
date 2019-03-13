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

namespace Sonata\CustomerBundle\Form\Type;

use Sonata\BasketBundle\Form\AddressType as BasketAddressType;
use Sonata\Component\Basket\BasketInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Vincent Composieux <composieux@ekino.com>
 *
 * Address form type (used for customer addresses add/edit actions)
 */
class AddressType extends AbstractType
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $getter;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var BasketInterface
     */
    protected $basket;

    /**
     * @param string          $class  A class to apply getter
     * @param string          $getter A getter method name
     * @param string          $name   A form type name
     * @param BasketInterface $basket Sonata e-commerce basket instance
     */
    public function __construct($class, $getter, $name, BasketInterface $basket)
    {
        $this->class = $class;
        $this->getter = $getter;
        $this->name = $name;
        $this->basket = $basket;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $address = $builder->getData();

        $countryOptions = [];
        $countries = [];

        if ('delivery' === $options['context'] && $address) {
            $countries = $this->getBasketDeliveryCountries();
        }

        if (\count($countries) > 0) {
            $countryOptions['choices'] = array_flip($countries);
        }

        $builder->add('countryCode', CountryType::class, $countryOptions);
    }

    public function getParent()
    {
        return BasketAddressType::class;
    }

    public function getBlockPrefix()
    {
        return $this->name;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'context' => 'default',
            'types' => \call_user_func([$this->class, $this->getter]),
        ]);
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
