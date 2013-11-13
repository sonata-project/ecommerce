<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\Component\Form\EventListener;

use Sonata\Component\Form\EventListener\BasketResizeFormListener;
use Symfony\Component\Form\FormEvents;

/**
 * Class BasketResizeFormListenerTest
 *
 * @package Sonata\Tests\Component\Form
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class BasketResizeFormListenerTest extends \PHPUnit_Framework_TestCase
{

    public function testGetSubscribedEvents()
    {
        $expected = array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_BIND     => 'preBind'
        );

        $this->assertEquals($expected, BasketResizeFormListener::getSubscribedEvents());
    }

    public function testPreSetData()
    {
        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')->disableOriginalConstructor()->getMock();

        $factory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $factory->expects($this->any())
            ->method('createNamedBuilder')
            ->will($this->returnValue($builder));

        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');

        $formListener = new BasketResizeFormListener($factory, $basket);

        $formListener->preSetData($this->getFormEvent(true));
    }

    public function testPreBind()
    {
        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')->disableOriginalConstructor()->getMock();

        $factory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $factory->expects($this->any())
            ->method('createNamedBuilder')
            ->will($this->returnValue($builder));

        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->any())
            ->method('getBasketElements')
            ->will($this->returnValue($this->getBasketElements()));

        $formListener = new BasketResizeFormListener($factory, $basket);
        $formListener->preBind($this->getFormEvent());
    }

    public function testPreBindEmpty()
    {
        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')->disableOriginalConstructor()->getMock();

        $factory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $factory->expects($this->any())
            ->method('createNamedBuilder')
            ->will($this->returnValue($builder));

        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');
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
        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')->disableOriginalConstructor()->getMock();

        $factory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $factory->expects($this->any())
            ->method('createNamedBuilder')
            ->will($this->returnValue($builder));

        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');
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
        $formEvent = $this->getMockBuilder('Symfony\Component\Form\FormEvent')->disableOriginalConstructor()->getMock();
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
        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        if ($addIsCalled) {
            $form->expects($this->once())->method('add');
        }

        return $form;
    }

    protected function getBasketElements($elements = array())
    {
        if (!is_array($elements)) {
            return $elements;
        }

        $productProvider = $this->getMock('Sonata\Component\Product\ProductProviderInterface');
        $productProvider->expects($this->once())->method('defineBasketElementForm');

        $basketElement = $this->getMock('Sonata\Component\Basket\BasketElementInterface');
        $basketElement->expects($this->exactly(2))->method('getPosition');
        $basketElement->expects($this->once())
            ->method('getProductProvider')
            ->will($this->returnValue($productProvider));

        $elements[] = $basketElement;

        return $elements;
    }
}
