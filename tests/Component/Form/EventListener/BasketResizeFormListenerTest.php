<?php

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
use Sonata\Component\Form\EventListener\BasketResizeFormListener;
use Symfony\Component\Form\FormEvents;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class BasketResizeFormListenerTest extends TestCase
{
    public function testGetSubscribedEvents()
    {
        $expected = [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preBind',
        ];

        $this->assertEquals($expected, BasketResizeFormListener::getSubscribedEvents());
    }

    public function testPreSetData()
    {
        $builder = $this->createMock('Symfony\Component\Form\FormBuilder');

        $factory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $factory->expects($this->any())
            ->method('createNamedBuilder')
            ->will($this->returnValue($builder));

        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');

        $formListener = new BasketResizeFormListener($factory, $basket);

        $formListener->preSetData($this->getFormEvent(true));
    }

    public function testPreBind()
    {
        $builder = $this->createMock('Symfony\Component\Form\FormBuilder');

        $factory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $factory->expects($this->any())
            ->method('createNamedBuilder')
            ->will($this->returnValue($builder));

        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->any())
            ->method('getBasketElements')
            ->will($this->returnValue($this->getBasketElements()));

        $formListener = new BasketResizeFormListener($factory, $basket);
        $formListener->preBind($this->getFormEvent());
    }

    public function testPreBindEmpty()
    {
        $builder = $this->createMock('Symfony\Component\Form\FormBuilder');

        $factory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $factory->expects($this->any())
            ->method('createNamedBuilder')
            ->will($this->returnValue($builder));

        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->any())
            ->method('getBasketElements')
            ->will($this->returnValue($this->getBasketElements(null)));

        $formListener = new BasketResizeFormListener($factory, $basket);
        $formListener->preBind($this->getFormEvent(false, false));
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function testPreBindException()
    {
        $builder = $this->createMock('Symfony\Component\Form\FormBuilder');

        $factory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $factory->expects($this->any())
            ->method('createNamedBuilder')
            ->will($this->returnValue($builder));

        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');
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
        $formEvent = $this->createMock('Symfony\Component\Form\FormEvent');
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
        $form = $this->createMock('Symfony\Component\Form\Form');
        if ($addIsCalled) {
            $form->expects($this->once())->method('add');
        }

        return $form;
    }

    protected function getBasketElements($elements = [])
    {
        if (!is_array($elements)) {
            return $elements;
        }

        $productProvider = $this->createMock('Sonata\Component\Product\ProductProviderInterface');
        $productProvider->expects($this->once())->method('defineBasketElementForm');

        $basketElement = $this->createMock('Sonata\Component\Basket\BasketElementInterface');
        $basketElement->expects($this->exactly(2))->method('getPosition');
        $basketElement->expects($this->once())
            ->method('getProductProvider')
            ->will($this->returnValue($productProvider));

        $elements[] = $basketElement;

        return $elements;
    }
}
