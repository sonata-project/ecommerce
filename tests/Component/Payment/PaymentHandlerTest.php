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

use Doctrine\ORM\EntityNotFoundException;
use PHPUnit\Framework\TestCase;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Generator\ReferenceInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Order\OrderManagerInterface;
use Sonata\Component\Payment\InvalidTransactionException;
use Sonata\Component\Payment\PaymentHandler;
use Sonata\Component\Payment\PaymentInterface;
use Sonata\Component\Payment\PaymentSelectorInterface;
use Sonata\Component\Payment\TransactionManagerInterface;
use Sonata\Component\Transformer\BasketTransformer;
use Sonata\Component\Transformer\OrderTransformer;
use Sonata\NotificationBundle\Backend\BackendInterface;
use Sonata\NotificationBundle\Backend\RuntimeBackend;
use Sonata\PaymentBundle\Tests\Entity\Transaction;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class PaymentHandlerTest extends TestCase
{
    public function testHandleError(): void
    {
        $payment = $this->createMock(PaymentInterface::class);
        $payment->expects($this->once())
            ->method('getOrderReference')
            ->will($this->returnValue('42'));
        $payment->expects($this->once())
            ->method('isRequestValid')
            ->will($this->returnValue(true));
        $payment->expects($this->once())
            ->method('getTransformer')
            ->will($this->returnValue($this->createMock(OrderTransformer::class)));

        $order = $this->createMock(OrderInterface::class);

        $om = $this->createMock(OrderManagerInterface::class);
        $om->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($order));

        $ps = $this->createMock(PaymentSelectorInterface::class);
        $ps->expects($this->exactly(3))
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $ref = $this->createMock(ReferenceInterface::class);

        $tm = $this->createMock(TransactionManagerInterface::class);
        $tm->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new Transaction()));

        $nb = $this->createMock(RuntimeBackend::class);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $handler = new PaymentHandler($om, $ps, $ref, $tm, $nb, $eventDispatcher);

        $request = new Request();
        $basket = $this->createMock(BasketInterface::class);

        $errorOrder = $handler->handleError($request, $basket);

        $this->assertSame($errorOrder, $order);
    }

    public function testHandleErrorInvalidTransactionException(): void
    {
        $this->expectException(InvalidTransactionException::class);
        $this->expectExceptionMessage('Unable to find reference');

        $payment = $this->createMock(PaymentInterface::class);

        $order = $this->createMock(OrderInterface::class);

        $om = $this->createMock(OrderManagerInterface::class);

        $ps = $this->createMock(PaymentSelectorInterface::class);
        $ps->expects($this->exactly(2))
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $ref = $this->createMock(ReferenceInterface::class);

        $tm = $this->createMock(TransactionManagerInterface::class);
        $tm->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new Transaction()));

        $nb = $this->createMock(RuntimeBackend::class);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $handler = new PaymentHandler($om, $ps, $ref, $tm, $nb, $eventDispatcher);

        $request = new Request();
        $basket = $this->createMock(BasketInterface::class);

        $errorOrder = $handler->handleError($request, $basket);

        $this->assertSame($errorOrder, $order);
    }

    public function testHandleErrorInvalidTransactionException2(): void
    {
        $this->expectException(InvalidTransactionException::class);
        $this->expectExceptionMessage('Invalid check - order ref: 42');

        $payment = $this->createMock(PaymentInterface::class);
        $payment->expects($this->once())
            ->method('getOrderReference')
            ->will($this->returnValue('42'));
        $payment->expects($this->once())
            ->method('isRequestValid')
            ->will($this->returnValue(false));

        $order = $this->createMock(OrderInterface::class);
        $order->expects($this->any())
            ->method('getReference')
            ->will($this->returnValue('42'));

        $om = $this->createMock(OrderManagerInterface::class);
        $om->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($order));

        $ps = $this->createMock(PaymentSelectorInterface::class);
        $ps->expects($this->exactly(2))
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $ref = $this->createMock(ReferenceInterface::class);

        $tm = $this->createMock(TransactionManagerInterface::class);
        $tm->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new Transaction()));

        $nb = $this->createMock(RuntimeBackend::class);

        $backend = $this->createMock(BackendInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $handler = new PaymentHandler($om, $ps, $ref, $tm, $backend, $eventDispatcher);

        $request = new Request();
        $basket = $this->createMock(BasketInterface::class);

        $errorOrder = $handler->handleError($request, $basket);

        $this->assertSame($errorOrder, $order);
    }

    public function testHandleErrorEntityNotFoundException(): void
    {
        $this->expectException(EntityNotFoundException::class);

        $payment = $this->createMock(PaymentInterface::class);
        $payment->expects($this->once())
            ->method('getOrderReference')
            ->will($this->returnValue('42'));

        $om = $this->createMock(OrderManagerInterface::class);
        $om->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue(null));

        $ps = $this->createMock(PaymentSelectorInterface::class);
        $ps->expects($this->exactly(2))
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $ref = $this->createMock(ReferenceInterface::class);

        $tm = $this->createMock(TransactionManagerInterface::class);
        $tm->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new Transaction()));

        $nb = $this->createMock(RuntimeBackend::class);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $handler = new PaymentHandler($om, $ps, $ref, $tm, $nb, $eventDispatcher);

        $request = new Request();
        $basket = $this->createMock(BasketInterface::class);

        $errorOrder = $handler->handleError($request, $basket);

        $this->assertSame($errorOrder, null);
    }

    public function testHandleConfirmation(): void
    {
        $payment = $this->createMock(PaymentInterface::class);
        $payment->expects($this->once())
            ->method('getOrderReference')
            ->will($this->returnValue('42'));
        $payment->expects($this->once())
            ->method('isRequestValid')
            ->will($this->returnValue(true));

        $order = $this->createMock(OrderInterface::class);

        $om = $this->createMock(OrderManagerInterface::class);
        $om->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($order));

        $ps = $this->createMock(PaymentSelectorInterface::class);
        $ps->expects($this->exactly(2))
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $ref = $this->createMock(ReferenceInterface::class);

        $tm = $this->createMock(TransactionManagerInterface::class);
        $tm->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new Transaction()));

        $nb = $this->createMock(RuntimeBackend::class);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $handler = new PaymentHandler($om, $ps, $ref, $tm, $nb, $eventDispatcher);

        $request = new Request();
        $basket = $this->createMock(BasketInterface::class);

        $confirmOrder = $handler->handleConfirmation($request);

        $this->assertSame($confirmOrder, $order);
    }

    public function testGetSendbankOrder(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $basketTransformer = $this->createMock(BasketTransformer::class);
        $basketTransformer->expects($this->once())
            ->method('transformIntoOrder')
            ->will($this->returnValue($order));

        $payment = $this->createMock(PaymentInterface::class);
        $payment->expects($this->once())
            ->method('getTransformer')
            ->will($this->returnValue($basketTransformer));

        $om = $this->createMock(OrderManagerInterface::class);
        $om->expects($this->once())->method('save');

        $ps = $this->createMock(PaymentSelectorInterface::class);

        $ref = $this->createMock(ReferenceInterface::class);
        $ref->expects($this->once())->method('order');

        $tm = $this->createMock(TransactionManagerInterface::class);

        $nb = $this->createMock(RuntimeBackend::class);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $handler = new PaymentHandler($om, $ps, $ref, $tm, $nb, $eventDispatcher);

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects($this->once())
            ->method('getPaymentMethod')
            ->will($this->returnValue($payment));

        $sendbankOrder = $handler->getSendbankOrder($basket);

        $this->assertSame($order, $sendbankOrder);
    }

    public function testGetPaymentCallbackResponse(): void
    {
        $response = new Response();

        $payment = $this->createMock(PaymentInterface::class);
        $payment->expects($this->once())
            ->method('getOrderReference')
            ->will($this->returnValue('42'));
        $payment->expects($this->once())
            ->method('isRequestValid')
            ->will($this->returnValue(true));
        $payment->expects($this->once())
            ->method('callback')
            ->will($this->returnValue($response));

        $order = $this->createMock(OrderInterface::class);

        $om = $this->createMock(OrderManagerInterface::class);
        $om->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($order));

        $ps = $this->createMock(PaymentSelectorInterface::class);
        $ps->expects($this->exactly(3))
            ->method('getPayment')
            ->will($this->returnValue($payment));

        $ref = $this->createMock(ReferenceInterface::class);

        $tm = $this->createMock(TransactionManagerInterface::class);
        $tm->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new Transaction()));

        $nb = $this->createMock(RuntimeBackend::class);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $handler = new PaymentHandler($om, $ps, $ref, $tm, $nb, $eventDispatcher);

        $request = new Request();

        $cbResponse = $handler->getPaymentCallbackResponse($request);

        $this->assertSame($response, $cbResponse);
    }
}
