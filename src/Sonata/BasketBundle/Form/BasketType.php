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
use Symfony\Component\Form\FormBuilder;
use Sonata\Component\Basket\BasketInterface;

class BasketType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        // always clone the basket, so the one in session is never altered
        $basket = $builder->getData();

        if (!$basket instanceof BasketInterface) {
            throw new \RunTimeException('Please provide a BasketInterface instance');
        }

        $basketElementsBuilder = $builder->create('basketElements', 'form');

        // ask each product repository to populate an empty group field instance
        // so each line can be tweaked depends on the product logic
        foreach ($basket->getBasketElements() as $basketElement) {
            $basketElementBuilder = $basketElementsBuilder->create($basketElement->getPos(), 'form');
            $basketElementBuilder->setErrorBubbling(false);

            $provider = $basketElement->getProductProvider();

            $provider->defineBasketElementForm($basketElement, $basketElementBuilder);

            $basketElementsBuilder->add($basketElementBuilder);
        }

        $builder->add($basketElementsBuilder);
    }

    public function getName()
    {
        return 'sonata_basket';
    }
}
