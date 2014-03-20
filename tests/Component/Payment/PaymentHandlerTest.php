<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\Component\Payment;

use Sonata\Component\Payment\InvalidTransactionException;
use Sonata\Component\Payment\PaymentHandler;
use Sonata\Tests\PaymentBundle\Entity\Transaction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PaymentHandlerTest
 *
 * @package Sonata\Tests\Component\Payment
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class PaymentHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandleError()
    {
        $payment = $this->getMock('Sonata\Component\Payment\PaymentInterface');
        $payment->expects($this->once())
            ->method('getOrderReference')
            ->will($this->returnValue('42'));
        $payment->expects($this->once())
            ->method('isRequestValid')
            ->will($this->returnValue(true));
        $payment->expects($this->once())
            ->method('getTransformer')
            ->will($this->returnValue($this->getMockBuilder('Sonata\Component\Transformer\OrderTransformer')->disableOriginalConstructor()->getMock()));

        $order = $this->getMock('Sonata\Component\Order\OrderInterface');

        $om  = $this->getMock('Sonata\Component\Order\OrderManagerInterface');
        $om->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($order));

        $ps  = $this->getMock('Sonata\Component\Payment\PaymentSelectorInterface');
        $ps->expects($this->exactly(3))
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $ref = $this->getMock('Sonata\Component\Generator\ReferenceInterface');

        $tm  = $this->getMock('Sonata\Component\Payment\TransactionManagerInterface');
        $tm->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new Transaction()));

        $nb = $this->getMockBuilder('Sonata\NotificationBundle\Backend\RuntimeBackend')->disableOriginalConstructor()->getMock();

        $eventDispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $handler = new PaymentHandler($om, $ps, $ref, $tm, $nb, $eventDispatcher);

        $request = new Request();
        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');

        $errorOrder = $handler->handleError($request, $basket);

        $this->assertEquals($errorOrder, $order);
    }

    /**
     * @expectedException Sonata\Component\Payment\InvalidTransactionException
     * @expectedExceptionMessage Unable to find reference
     */
    public function testHandleErrorInvalidTransactionException()
    {
        $payment = $this->getMock('Sonata\Component\Payment\PaymentInterface');

        $order = $this->getMock('Sonata\Component\Order\OrderInterface');

        $om  = $this->getMock('Sonata\Component\Order\OrderManagerInterface');

        $ps  = $this->getMock('Sonata\Component\Payment\PaymentSelectorInterface');
        $ps->expects($this->exactly(2))
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $ref = $this->getMock('Sonata\Component\Generator\ReferenceInterface');

        $tm  = $this->getMock('Sonata\Component\Payment\TransactionManagerInterface');
        $tm->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new Transaction()));

        $nb = $this->getMockBuilder('Sonata\NotificationBundle\Backend\RuntimeBackend')->disableOriginalConstructor()->getMock();

        $eventDispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $handler = new PaymentHandler($om, $ps, $ref, $tm, $nb, $eventDispatcher);

        $request = new Request();
        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');

        $errorOrder = $handler->handleError($request, $basket);

        $this->assertEquals($errorOrder, $order);
    }

    /**
     * @expectedException Sonata\Component\Payment\InvalidTransactionException
     * @expectedExceptionMessage Invalid check - order ref: 42
     */
    public function testHandleErrorInvalidTransactionException2()
    {
        $payment = $this->getMock('Sonata\Component\Payment\PaymentInterface');
        $payment->expects($this->once())
            ->method('getOrderReference')
            ->will($this->returnValue('42'));
        $payment->expects($this->once())
            ->method('isRequestValid')
            ->will($this->returnValue(false));

        $order = $this->getMock('Sonata\Component\Order\OrderInterface');
        $order->expects($this->any())
            ->method('getReference')
            ->will($this->returnValue('42'));

        $om  = $this->getMock('Sonata\Component\Order\OrderManagerInterface');
        $om->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($order));

        $ps  = $this->getMock('Sonata\Component\Payment\PaymentSelectorInterface');
        $ps->expects($this->exactly(2))
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $ref = $this->getMock('Sonata\Component\Generator\ReferenceInterface');

        $tm  = $this->getMock('Sonata\Component\Payment\TransactionManagerInterface');
        $tm->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new Transaction()));

        $nb = $this->getMockBuilder('Sonata\NotificationBundle\Backend\RuntimeBackend')->disableOriginalConstructor()->getMock();

        $backend = $this->getMock('Sonata\NotificationBundle\Backend\BackendInterface');
        $eventDispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $handler = new PaymentHandler($om, $ps, $ref, $tm, $backend, $eventDispatcher);

        $request = new Request();
        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');

        $errorOrder = $handler->handleError($request, $basket);

        $this->assertEquals($errorOrder, $order);
    }

    /**
     * @expectedException \Doctrine\ORM\EntityNotFoundException
     */
    public function testHandleErrorEntityNotFoundException()
    {
        $payment = $this->getMock('Sonata\Component\Payment\PaymentInterface');
        $payment->expects($this->once())
            ->method('getOrderReference')
            ->will($this->returnValue('42'));

        $om  = $this->getMock('Sonata\Component\Order\OrderManagerInterface');
        $om->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue(null));

        $ps  = $this->getMock('Sonata\Component\Payment\PaymentSelectorInterface');
        $ps->expects($this->exactly(2))
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $ref = $this->getMock('Sonata\Component\Generator\ReferenceInterface');

        $tm  = $this->getMock('Sonata\Component\Payment\TransactionManagerInterface');
        $tm->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new Transaction()));

        $nb = $this->getMockBuilder('Sonata\NotificationBundle\Backend\RuntimeBackend')->disableOriginalConstructor()->getMock();

        $eventDispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $handler = new PaymentHandler($om, $ps, $ref, $tm, $nb, $eventDispatcher);

        $request = new Request();
        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');

        $errorOrder = $handler->handleError($request, $basket);

        $this->assertEquals($errorOrder, null);
    }

    public function testHandleConfirmation()
    {
        $payment = $this->getMock('Sonata\Component\Payment\PaymentInterface');
        $payment->expects($this->once())
            ->method('getOrderReference')
            ->will($this->returnValue('42'));
        $payment->expects($this->once())
            ->method('isRequestValid')
            ->will($this->returnValue(true));

        $order = $this->getMock('Sonata\Component\Order\OrderInterface');

        $om  = $this->getMock('Sonata\Component\Order\OrderManagerInterface');
        $om->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($order));

        $ps  = $this->getMock('Sonata\Component\Payment\PaymentSelectorInterface');
        $ps->expects($this->exactly(2))
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $ref = $this->getMock('Sonata\Component\Generator\ReferenceInterface');

        $tm  = $this->getMock('Sonata\Component\Payment\TransactionManagerInterface');
        $tm->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new Transaction()));

        $nb = $this->getMockBuilder('Sonata\NotificationBundle\Backend\RuntimeBackend')->disableOriginalConstructor()->getMock();

        $eventDispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $handler = new PaymentHandler($om, $ps, $ref, $tm, $nb, $eventDispatcher);

        $request = new Request();
        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');

        $confirmOrder = $handler->handleConfirmation($request);

        $this->assertEquals($confirmOrder, $order);
    }

    public function testGetSendbankOrder()
    {
        $order = $this->getMock('Sonata\Component\Order\OrderInterface');

        $basketTransformer = $this->getMockBuilder('Sonata\Component\Transformer\BasketTransformer')->disableOriginalConstructor()->getMock();
        $basketTransformer->expects($this->once())
            ->method('transformIntoOrder')
            ->will($this->returnValue($order));

        $payment = $this->getMock('Sonata\Component\Payment\PaymentInterface');
        $payment->expects($this->once())
            ->method('getTransformer')
            ->will($this->returnValue($basketTransformer));

        $om  = $this->getMock('Sonata\Component\Order\OrderManagerInterface');
        $om->expects($this->once())->method('save');

        $ps  = $this->getMock('Sonata\Component\Payment\PaymentSelectorInterface');

        $ref = $this->getMock('Sonata\Component\Generator\ReferenceInterface');
        $ref->expects($this->once())->method('order');

        $tm  = $this->getMock('Sonata\Component\Payment\TransactionManagerInterface');

        $nb = $this->getMockBuilder('Sonata\NotificationBundle\Backend\RuntimeBackend')->disableOriginalConstructor()->getMock();

        $eventDispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $handler = new PaymentHandler($om, $ps, $ref, $tm, $nb, $eventDispatcher);

        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())
            ->method('getPaymentMethod')
            ->will($this->returnValue($payment));

        $sendbankOrder = $handler->getSendbankOrder($basket);

        $this->assertEquals($order, $sendbankOrder);
    }

    public function testGetPaymentCallbackResponse()
    {
        $response = new Response();

        $payment = $this->getMock('Sonata\Component\Payment\PaymentInterface');
        $payment->expects($this->once())
            ->method('getOrderReference')
            ->will($this->returnValue('42'));
        $payment->expects($this->once())
            ->method('isRequestValid')
            ->will($this->returnValue(true));
        $payment->expects($this->once())
            ->method('callback')
            ->will($this->returnValue($response));

        $order = $this->getMock('Sonata\Component\Order\OrderInterface');

        $om  = $this->getMock('Sonata\Component\Order\OrderManagerInterface');
        $om->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($order));

        $ps  = $this->getMock('Sonata\Component\Payment\PaymentSelectorInterface');
        $ps->expects($this->exactly(3))
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $ref = $this->getMock('Sonata\Component\Generator\ReferenceInterface');

        $tm  = $this->getMock('Sonata\Component\Payment\TransactionManagerInterface');
        $tm->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new Transaction()));

        $nb = $this->getMockBuilder('Sonata\NotificationBundle\Backend\RuntimeBackend')->disableOriginalConstructor()->getMock();

        $eventDispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $handler = new PaymentHandler($om, $ps, $ref, $tm, $nb, $eventDispatcher);

        $request = new Request();

        $cbResponse = $handler->getPaymentCallbackResponse($request);

        $this->assertEquals($response, $cbResponse);
    }
}
