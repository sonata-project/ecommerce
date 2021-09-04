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

        static::assertSame($expected, BasketResizeFormListener::getSubscribedEvents());
    }

    public function testPreSetData(): void
    {
        $builder = $this->createMock(FormBuilder::class);

        $factory = $this->createMock(FormFactoryInterface::class);
        $factory->expects(static::any())
            ->method('createNamedBuilder')
            ->willReturn($builder);

        $basket = $this->createMock(BasketInterface::class);

        $formListener = new BasketResizeFormListener($factory, $basket);

        $formListener->preSetData($this->getFormEvent(true));
    }

    public function testPreBind(): void
    {
        $builder = $this->createMock(FormBuilder::class);

        $factory = $this->createMock(FormFactoryInterface::class);
        $factory->expects(static::any())
            ->method('createNamedBuilder')
            ->willReturn($builder);

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects(static::any())
            ->method('getBasketElements')
            ->willReturn($this->getBasketElements());

        $formListener = new BasketResizeFormListener($factory, $basket);
        $formListener->preBind($this->getFormEvent());
    }

    public function testPreBindEmpty(): void
    {
        $builder = $this->createMock(FormBuilder::class);

        $factory = $this->createMock(FormFactoryInterface::class);
        $factory->expects(static::any())
            ->method('createNamedBuilder')
            ->willReturn($builder);

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects(static::any())
            ->method('getBasketElements')
            ->willReturn($this->getBasketElements(null));

        $formListener = new BasketResizeFormListener($factory, $basket);
        $formListener->preBind($this->getFormEvent(false, false));
    }

    public function testPreBindException(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $builder = $this->createMock(FormBuilder::class);

        $factory = $this->createMock(FormFactoryInterface::class);
        $factory->expects(static::any())
            ->method('createNamedBuilder')
            ->willReturn($builder);

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects(static::any())
            ->method('getBasketElements')
            ->willReturn($this->getBasketElements('test'));

        $formListener = new BasketResizeFormListener($factory, $basket);
        $formListener->preBind($this->getFormEvent(false, false));
    }

    /**
     * @return \Symfony\Component\Form\FormEvent
     */
    protected function getFormEvent($preSetData = false, $addIsCalled = true)
    {
        $formEvent = $this->createMock(FormEvent::class);
        $formEvent->expects(static::once())
            ->method('getForm')
            ->willReturn($this->getMockedForm($addIsCalled));

        if ($preSetData) {
            $formEvent->expects(static::once())
                ->method('getData')
                ->willReturn($this->getBasketElements());
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
            $form->expects(static::once())->method('add');
        }

        return $form;
    }

    protected function getBasketElements($elements = [])
    {
        if (!\is_array($elements)) {
            return $elements;
        }

        $productProvider = $this->createMock(ProductProviderInterface::class);
        $productProvider->expects(static::once())->method('defineBasketElementForm');

        $basketElement = $this->createMock(BasketElementInterface::class);
        $basketElement->expects(static::exactly(2))->method('getPosition');
        $basketElement->expects(static::once())
            ->method('getProductProvider')
            ->willReturn($productProvider);

        $elements[] = $basketElement;

        return $elements;
    }
}
