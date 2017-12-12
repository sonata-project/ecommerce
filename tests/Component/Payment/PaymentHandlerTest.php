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

namespace Sonata\Component\Tests\Payment;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Payment\PaymentHandler;
use Sonata\PaymentBundle\Tests\Entity\Transaction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class PaymentHandlerTest extends TestCase
{
    public function testHandleError(): void
    {
        $payment = $this->createMock('Sonata\Component\Payment\PaymentInterface');
        $payment->expects($this->once())
            ->method('getOrderReference')
            ->will($this->returnValue('42'));
        $payment->expects($this->once())
            ->method('isRequestValid')
            ->will($this->returnValue(true));
        $payment->expects($this->once())
            ->method('getTransformer')
            ->will($this->returnValue($this->createMock('Sonata\Component\Transformer\OrderTransformer')));

        $order = $this->createMock('Sonata\Component\Order\OrderInterface');

        $om = $this->createMock('Sonata\Component\Order\OrderManagerInterface');
        $om->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($order));

        $ps = $this->createMock('Sonata\Component\Payment\PaymentSelectorInterface');
        $ps->expects($this->exactly(3))
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $ref = $this->createMock('Sonata\Component\Generator\ReferenceInterface');

        $tm = $this->createMock('Sonata\Component\Payment\TransactionManagerInterface');
        $tm->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new Transaction()));

        $nb = $this->createMock('Sonata\NotificationBundle\Backend\RuntimeBackend');

        $eventDispatcher = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $handler = new PaymentHandler($om, $ps, $ref, $tm, $nb, $eventDispatcher);

        $request = new Request();
        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');

        $errorOrder = $handler->handleError($request, $basket);

        $this->assertEquals($errorOrder, $order);
    }

    public function testHandleErrorInvalidTransactionException(): void
    {
        $this->expectException(\Sonata\Component\Payment\InvalidTransactionException::class);
        $this->expectExceptionMessage('Unable to find reference');

        $payment = $this->createMock('Sonata\Component\Payment\PaymentInterface');

        $order = $this->createMock('Sonata\Component\Order\OrderInterface');

        $om = $this->createMock('Sonata\Component\Order\OrderManagerInterface');

        $ps = $this->createMock('Sonata\Component\Payment\PaymentSelectorInterface');
        $ps->expects($this->exactly(2))
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $ref = $this->createMock('Sonata\Component\Generator\ReferenceInterface');

        $tm = $this->createMock('Sonata\Component\Payment\TransactionManagerInterface');
        $tm->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new Transaction()));

        $nb = $this->createMock('Sonata\NotificationBundle\Backend\RuntimeBackend');

        $eventDispatcher = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $handler = new PaymentHandler($om, $ps, $ref, $tm, $nb, $eventDispatcher);

        $request = new Request();
        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');

        $errorOrder = $handler->handleError($request, $basket);

        $this->assertEquals($errorOrder, $order);
    }

    public function testHandleErrorInvalidTransactionException2(): void
    {
        $this->expectException(\Sonata\Component\Payment\InvalidTransactionException::class);
        $this->expectExceptionMessage('Invalid check - order ref: 42');

        $payment = $this->createMock('Sonata\Component\Payment\PaymentInterface');
        $payment->expects($this->once())
            ->method('getOrderReference')
            ->will($this->returnValue('42'));
        $payment->expects($this->once())
            ->method('isRequestValid')
            ->will($this->returnValue(false));

        $order = $this->createMock('Sonata\Component\Order\OrderInterface');
        $order->expects($this->any())
            ->method('getReference')
            ->will($this->returnValue('42'));

        $om = $this->createMock('Sonata\Component\Order\OrderManagerInterface');
        $om->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($order));

        $ps = $this->createMock('Sonata\Component\Payment\PaymentSelectorInterface');
        $ps->expects($this->exactly(2))
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $ref = $this->createMock('Sonata\Component\Generator\ReferenceInterface');

        $tm = $this->createMock('Sonata\Component\Payment\TransactionManagerInterface');
        $tm->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new Transaction()));

        $nb = $this->createMock('Sonata\NotificationBundle\Backend\RuntimeBackend');

        $backend = $this->createMock('Sonata\NotificationBundle\Backend\BackendInterface');
        $eventDispatcher = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $handler = new PaymentHandler($om, $ps, $ref, $tm, $backend, $eventDispatcher);

        $request = new Request();
        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');

        $errorOrder = $handler->handleError($request, $basket);

        $this->assertEquals($errorOrder, $order);
    }

    public function testHandleErrorEntityNotFoundException(): void
    {
        $this->expectException(\Doctrine\ORM\EntityNotFoundException::class);

        $payment = $this->createMock('Sonata\Component\Payment\PaymentInterface');
        $payment->expects($this->once())
            ->method('getOrderReference')
            ->will($this->returnValue('42'));

        $om = $this->createMock('Sonata\Component\Order\OrderManagerInterface');
        $om->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue(null));

        $ps = $this->createMock('Sonata\Component\Payment\PaymentSelectorInterface');
        $ps->expects($this->exactly(2))
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $ref = $this->createMock('Sonata\Component\Generator\ReferenceInterface');

        $tm = $this->createMock('Sonata\Component\Payment\TransactionManagerInterface');
        $tm->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new Transaction()));

        $nb = $this->createMock('Sonata\NotificationBundle\Backend\RuntimeBackend');

        $eventDispatcher = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $handler = new PaymentHandler($om, $ps, $ref, $tm, $nb, $eventDispatcher);

        $request = new Request();
        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');

        $errorOrder = $handler->handleError($request, $basket);

        $this->assertEquals($errorOrder, null);
    }

    public function testHandleConfirmation(): void
    {
        $payment = $this->createMock('Sonata\Component\Payment\PaymentInterface');
        $payment->expects($this->once())
            ->method('getOrderReference')
            ->will($this->returnValue('42'));
        $payment->expects($this->once())
            ->method('isRequestValid')
            ->will($this->returnValue(true));

        $order = $this->createMock('Sonata\Component\Order\OrderInterface');

        $om = $this->createMock('Sonata\Component\Order\OrderManagerInterface');
        $om->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($order));

        $ps = $this->createMock('Sonata\Component\Payment\PaymentSelectorInterface');
        $ps->expects($this->exactly(2))
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $ref = $this->createMock('Sonata\Component\Generator\ReferenceInterface');

        $tm = $this->createMock('Sonata\Component\Payment\TransactionManagerInterface');
        $tm->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new Transaction()));

        $nb = $this->createMock('Sonata\NotificationBundle\Backend\RuntimeBackend');

        $eventDispatcher = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $handler = new PaymentHandler($om, $ps, $ref, $tm, $nb, $eventDispatcher);

        $request = new Request();
        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');

        $confirmOrder = $handler->handleConfirmation($request);

        $this->assertEquals($confirmOrder, $order);
    }

    public function testGetSendbankOrder(): void
    {
        $order = $this->createMock('Sonata\Component\Order\OrderInterface');

        $basketTransformer = $this->createMock('Sonata\Component\Transformer\BasketTransformer');
        $basketTransformer->expects($this->once())
            ->method('transformIntoOrder')
            ->will($this->returnValue($order));

        $payment = $this->createMock('Sonata\Component\Payment\PaymentInterface');
        $payment->expects($this->once())
            ->method('getTransformer')
            ->will($this->returnValue($basketTransformer));

        $om = $this->createMock('Sonata\Component\Order\OrderManagerInterface');
        $om->expects($this->once())->method('save');

        $ps = $this->createMock('Sonata\Component\Payment\PaymentSelectorInterface');

        $ref = $this->createMock('Sonata\Component\Generator\ReferenceInterface');
        $ref->expects($this->once())->method('order');

        $tm = $this->createMock('Sonata\Component\Payment\TransactionManagerInterface');

        $nb = $this->createMock('Sonata\NotificationBundle\Backend\RuntimeBackend');

        $eventDispatcher = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $handler = new PaymentHandler($om, $ps, $ref, $tm, $nb, $eventDispatcher);

        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())
            ->method('getPaymentMethod')
            ->will($this->returnValue($payment));

        $sendbankOrder = $handler->getSendbankOrder($basket);

        $this->assertEquals($order, $sendbankOrder);
    }

    public function testGetPaymentCallbackResponse(): void
    {
        $response = new Response();

        $payment = $this->createMock('Sonata\Component\Payment\PaymentInterface');
        $payment->expects($this->once())
            ->method('getOrderReference')
            ->will($this->returnValue('42'));
        $payment->expects($this->once())
            ->method('isRequestValid')
            ->will($this->returnValue(true));
        $payment->expects($this->once())
            ->method('callback')
            ->will($this->returnValue($response));

        $order = $this->createMock('Sonata\Component\Order\OrderInterface');

        $om = $this->createMock('Sonata\Component\Order\OrderManagerInterface');
        $om->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($order));

        $ps = $this->createMock('Sonata\Component\Payment\PaymentSelectorInterface');
        $ps->expects($this->exactly(3))
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $ref = $this->createMock('Sonata\Component\Generator\ReferenceInterface');

        $tm = $this->createMock('Sonata\Component\Payment\TransactionManagerInterface');
        $tm->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new Transaction()));

        $nb = $this->createMock('Sonata\NotificationBundle\Backend\RuntimeBackend');

        $eventDispatcher = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $handler = new PaymentHandler($om, $ps, $ref, $tm, $nb, $eventDispatcher);

        $request = new Request();

        $cbResponse = $handler->getPaymentCallbackResponse($request);

        $this->assertEquals($response, $cbResponse);
    }
}
