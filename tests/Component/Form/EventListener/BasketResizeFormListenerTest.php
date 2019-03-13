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

namespace Sonata\Component\Tests\Form\EventListener;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Basket\BasketElementInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Form\EventListener\BasketResizeFormListener;
use Sonata\Component\Product\ProductProviderInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class BasketResizeFormListenerTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        $expected = [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preBind',
        ];

        $this->assertSame($expected, BasketResizeFormListener::getSubscribedEvents());
    }

    public function testPreSetData(): void
    {
        $builder = $this->createMock(FormBuilder::class);

        $factory = $this->createMock(FormFactoryInterface::class);
        $factory->expects($this->any())
            ->method('createNamedBuilder')
            ->will($this->returnValue($builder));

        $basket = $this->createMock(BasketInterface::class);

        $formListener = new BasketResizeFormListener($factory, $basket);

        $formListener->preSetData($this->getFormEvent(true));
    }

    public function testPreBind(): void
    {
        $builder = $this->createMock(FormBuilder::class);

        $factory = $this->createMock(FormFactoryInterface::class);
        $factory->expects($this->any())
            ->method('createNamedBuilder')
            ->will($this->returnValue($builder));

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects($this->any())
            ->method('getBasketElements')
            ->will($this->returnValue($this->getBasketElements()));

        $formListener = new BasketResizeFormListener($factory, $basket);
        $formListener->preBind($this->getFormEvent());
    }

    public function testPreBindEmpty(): void
    {
        $builder = $this->createMock(FormBuilder::class);

        $factory = $this->createMock(FormFactoryInterface::class);
        $factory->expects($this->any())
            ->method('createNamedBuilder')
            ->will($this->returnValue($builder));

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects($this->any())
            ->method('getBasketElements')
            ->will($this->returnValue($this->getBasketElements(null)));

        $formListener = new BasketResizeFormListener($factory, $basket);
        $formListener->preBind($this->getFormEvent(false, false));
    }

    public function testPreBindException(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $builder = $this->createMock(FormBuilder::class);

        $factory = $this->createMock(FormFactoryInterface::class);
        $factory->expects($this->any())
            ->method('createNamedBuilder')
            ->will($this->returnValue($builder));

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects($this->any())
            ->method('getBasketElements')
            ->will($this->returnValue($this->getBasketElements('test')));

        $formListener = new BasketResizeFormListener($factory, $basket);
        $formListener->preBind($this->getFormEvent(false, false));
    }

    /**
     * @return \Symfony\Component\Form\FormEvent
     */
    protected function getFormEvent($preSetData = false, $addIsCalled = true)
    {
        $formEvent = $this->createMock(FormEvent::class);
        $formEvent->expects($this->once())
            ->method('getForm')
            ->will($this->returnValue($this->getMockedForm($addIsCalled)));

        if ($preSetData) {
            $formEvent->expects($this->once())
                ->method('getData')
                ->will($this->returnValue($this->getBasketElements()));
        }

        return $formEvent;
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function getMockedForm($addIsCalled = true)
    {
        $form = $this->createMock(Form::class);
        if ($addIsCalled) {
            $form->expects($this->once())->method('add');
        }

        return $form;
    }

    protected function getBasketElements($elements = [])
    {
        if (!\is_array($elements)) {
            return $elements;
        }

        $productProvider = $this->createMock(ProductProviderInterface::class);
        $productProvider->expects($this->once())->method('defineBasketElementForm');

        $basketElement = $this->createMock(BasketElementInterface::class);
        $basketElement->expects($this->exactly(2))->method('getPosition');
        $basketElement->expects($this->once())
            ->method('getProductProvider')
            ->will($this->returnValue($productProvider));

        $elements[] = $basketElement;

        return $elements;
    }
}
