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

use Sonata\Component\Currency\CurrencyFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class ApiBasketType extends AbstractType
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var CurrencyFormType
     */
    protected $currencyFormType;

    /**
     * @param string           $class            An entity data class
     * @param CurrencyFormType $currencyFormType A Sonata ecommerce currency form type
     */
    public function __construct($class, CurrencyFormType $currencyFormType)
    {
        $this->class = $class;
        $this->currencyFormType = $currencyFormType;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            $builder->create('currency', $this->currencyFormType)
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => $this->class,
            'csrf_protection' => false,
            'validation_groups' => ['api'],
        ]);
    }

    public function getBlockPrefix()
    {
        return 'sonata_basket_api_form_basket';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getParent()
    {
        return ApiBasketParentType::class;
    }
}
