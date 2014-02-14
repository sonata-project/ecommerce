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
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Form\EventListener\BasketResizeFormListener;
use Symfony\Component\Form\FormBuilderInterface;

class BasketType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // always clone the basket, so the one in session is never altered
        $basket = $builder->getData();

        if (!$basket instanceof BasketInterface) {
            throw new \RunTimeException('Please provide a BasketInterface instance');
        }

        // should create a custom basket elements here
        $basketElementBuilder = $builder->create('basketElements', 'form', array(
            'by_reference' => false
        ));
        $basketElementBuilder->addEventSubscriber(new BasketResizeFormListener($builder->getFormFactory(), $basket));
        $builder->add($basketElementBuilder);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata_basket_basket';
    }
}
