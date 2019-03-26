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
use Psr\Log\LoggerInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Payment\Paypal;
use Sonata\Component\Payment\TransactionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class PaypalTest extends TestCase
{
    public function testSendbank(): void
    {
        $router = $this->createMock(RouterInterface::class);
        $translator = $this->createMock(TranslatorInterface::class);

        $paypal = new Paypal($router, $translator);
        $options = [
            'cert_file' => __DIR__.'/PaypalTestFiles/cert_file',
            'key_file' => __DIR__.'/PaypalTestFiles/key_file',
            'paypal_cert_file' => __DIR__.'/PaypalTestFiles/paypal_cert_file',
            'openssl' => __DIR__.'/PaypalTestFiles/openssl',
        ];
        $paypal->setOptions($options);

        $order = $this->createMock(OrderInterface::class);
        $order->expects($this->any())->method('getCreatedAt')->will($this->returnValue(new \DateTime()));

        $sendbank = $paypal->sendbank($order);
        $this->assertInstanceOf(Response::class, $sendbank);

        $this->assertContains('<input type="hidden" name="cmd" value="_s-xclick">', $sendbank->getContent());
    }

    public function testIsCallbackValid(): void
    {
        $router = $this->createMock(RouterInterface::class);
        $translator = $this->createMock(TranslatorInterface::class);

        $paypal = new Paypal($router, $translator);

        $order = $this->createMock(OrderInterface::class);
        $order->expects($this->any())->method('getCreatedAt')->will($this->returnValue(new \DateTime()));
        $order->expects($this->any())->method('isValidated')->will($this->returnValue(true));

        $transaction = $this->createMock(TransactionInterface::class);
        $transaction->expects($this->any())->method('getOrder')->will($this->returnValue($order));

        $this->assertFalse($paypal->isCallbackValid($transaction), 'Paypal::isCallbackValid false because request invalid');

        $check = sha1(
            $order->getReference().
            $order->getCreatedAt()->format('m/d/Y:G:i:s').
            $order->getId()
        );
        $transaction->expects($this->any())->method('get')->will($this->returnValue($check));

        $this->assertFalse($paypal->isCallbackValid($transaction), 'Paypal::isCallbackValid false because order not validated');

        $order = $this->createMock(OrderInterface::class);
        $order->expects($this->any())->method('getCreatedAt')->will($this->returnValue(new \DateTime()));
        $order->expects($this->any())->method('isValidated')->will($this->returnValue(false));

        $check = sha1(
            $order->getReference().
            $order->getCreatedAt()->format('m/d/Y:G:i:s').
            $order->getId()
        );

        $transaction = $this->createMock(TransactionInterface::class);
        $transaction->expects($this->any())->method('getOrder')->will($this->returnValue($order));
        $transaction->expects($this->any())->method('get')->will($this->returnValue($check));

        $this->assertFalse($paypal->isCallbackValid($transaction), 'Paypal::isCallbackValid false because payment_status invalid.');

        $transaction = $this->createMock(TransactionInterface::class);
        $transaction->expects($this->any())->method('getOrder')->will($this->returnValue($order));

        $transaction->expects($this->any())->method('get')->will($this->returnCallback(function () use ($check) {
            $asked = func_get_arg(0);
            switch ($asked) {
                        case 'check':
                            return $check;
                        case 'payment_status':
                            return 'Pending';
                    }
        }));

        $this->assertTrue($paypal->isCallbackValid($transaction), 'Paypal::isCallbackValid true because payment_status pending.');

        $transaction = $this->createMock(TransactionInterface::class);
        $transaction->expects($this->any())->method('getOrder')->will($this->returnValue($order));

        $transaction->expects($this->any())->method('get')->will($this->returnCallback(function () use ($check) {
            $asked = func_get_arg(0);
            switch ($asked) {
                        case 'check':
                            return $check;
                        case 'payment_status':
                            return 'Completed';
                    }
        }));

        $this->assertTrue($paypal->isCallbackValid($transaction), 'Paypal::isCallbackValid true because payment_status completed.');

        $transaction = $this->createMock(TransactionInterface::class);
        $transaction->expects($this->any())->method('getOrder')->will($this->returnValue($order));

        $transaction->expects($this->any())->method('get')->will($this->returnCallback(function () use ($check) {
            $asked = func_get_arg(0);
            switch ($asked) {
                        case 'check':
                            return $check;
                        case 'payment_status':
                            return 'Cancelled';
                    }
        }));

        $this->assertTrue($paypal->isCallbackValid($transaction), 'Paypal::isCallbackValid true because payment_status cancelled.');
    }

    public function testHandleError(): void
    {
        $router = $this->createMock(RouterInterface::class);
        $translator = $this->createMock(TranslatorInterface::class);

        $paypal = new Paypal($router, $translator);
        $paypal->setLogger($this->createMock(LoggerInterface::class));

        $order = $this->createMock(OrderInterface::class);

        $transaction = $this->createMock(TransactionInterface::class);
        $transaction->expects($this->once())->method('getOrder')->will($this->returnValue($order));

        $paypal->handleError($transaction);

        $transaction = $this->createMock(TransactionInterface::class);
        $transaction->expects($this->once())->method('getOrder')->will($this->returnValue($order));
        $transaction->expects($this->atLeastOnce())
                    ->method('getStatusCode')
                    ->will($this->returnValue(TransactionInterface::STATUS_ORDER_UNKNOWN));

        $paypal->handleError($transaction);

        $transaction = $this->createMock(TransactionInterface::class);
        $transaction->expects($this->atLeastOnce())->method('getOrder')->will($this->returnValue($order));
        $transaction->expects($this->atLeastOnce())
                    ->method('getStatusCode')
                    ->will($this->returnValue(TransactionInterface::STATUS_ERROR_VALIDATION));

        $paypal->handleError($transaction);

        $transaction = $this->createMock(TransactionInterface::class);
        $transaction->expects($this->atLeastOnce())->method('getOrder')->will($this->returnValue($order));
        $transaction->expects($this->atLeastOnce())
                    ->method('getStatusCode')
                    ->will($this->returnValue(TransactionInterface::STATUS_CANCELLED));

        $paypal->handleError($transaction);

        $transaction = $this->createMock(TransactionInterface::class);
        $transaction->expects($this->atLeastOnce())->method('getOrder')->will($this->returnValue($order));
        $transaction->expects($this->atLeastOnce())
                    ->method('getStatusCode')
                    ->will($this->returnValue(TransactionInterface::STATUS_PENDING));
        $transaction->expects($this->atLeastOnce())
                    ->method('get')
                    ->will($this->returnValue(Paypal::PENDING_REASON_ADDRESS));

        $paypal->handleError($transaction);
    }
}
