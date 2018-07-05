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

namespace Sonata\Component\Form\EventListener;

use Sonata\Component\Basket\BasketInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

class BasketResizeFormListener implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * @var BasketInterface
     */
    private $basket;

    /**
     * @var array
     */
    private $removed = [];

    public function __construct(FormFactoryInterface $factory, BasketInterface $basket)
    {
        $this->factory = $factory;
        $this->basket = $basket;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preBind',
        ];
    }

    public function preSetData(FormEvent $event): void
    {
        $form = $event->getForm();
        $basketElements = $event->getData();

        $this->buildBasketElements($form, $basketElements);
    }

    /**
     * @param FormEvent $event
     */
    public function preBind(FormEvent $event): void
    {
        $form = $event->getForm();
        $data = $event->getData();

        $this->buildBasketElements($form, $this->basket->getBasketElements());
    }

    /**
     * @param $form
     * @param $basketElements
     *
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    private function buildBasketElements($form, $basketElements): void
    {
        if (null === $basketElements) {
            return;
        }

        if (!$basketElements instanceof \ArrayAccess && !is_array($basketElements)) {
            throw new UnexpectedTypeException($basketElements, 'array or \ArrayAccess');
        }

        foreach ($basketElements as $basketElement) {
            $basketElementBuilder = $this->factory->createNamedBuilder($basketElement->getPosition(), 'form', $basketElement, [
                'property_path' => '['.$basketElement->getPosition().']',
                'auto_initialize' => false,
            ]);
            $basketElementBuilder->setErrorBubbling(false);

            $provider = $basketElement->getProductProvider();
            $provider->defineBasketElementForm($basketElement, $basketElementBuilder);

            $form->add($basketElementBuilder->getForm());
        }
    }
}
