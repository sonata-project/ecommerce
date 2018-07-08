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
use Sonata\Component\Form\EventListener\BasketResizeFormListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;

class BasketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // always clone the basket, so the one in session is never altered
        $basket = $builder->getData();

        if (!$basket instanceof BasketInterface) {
            throw new \RunTimeException('Please provide a BasketInterface instance');
        }

        // should create a custom basket elements here
        $basketElementBuilder = $builder->create('basketElements', FormType::class, [
            'by_reference' => false,
        ]);
        $basketElementBuilder->addEventSubscriber(new BasketResizeFormListener($builder->getFormFactory(), $basket));
        $builder->add($basketElementBuilder);
    }

    public function getBlockPrefix()
    {
        return 'sonata_basket_basket';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
