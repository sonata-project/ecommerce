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

use Buzz\Browser;
use Buzz\Client\ClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Sonata\Component\Basket\Basket;
use Sonata\Component\Payment\CheckPayment;
use Sonata\Component\Payment\TransactionInterface;
use Sonata\Component\Product\ProductInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class CheckPaymentTest extends TestCase
{
    /**
     * useless test ....
     */
    public function testPassPayment(): void
    {
        $router = $this->createMock(RouterInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $browser = new Browser();
        $payment = new CheckPayment($router, $logger, $browser);
        $payment->setCode('free_1');

        $basket = $this->createMock(Basket::class);
        $product = $this->createMock(ProductInterface::class);

        $date = new \DateTime('1981-11-30', new \DateTimeZone('Europe/Paris'));

        $order = new CheckPaymentTest_Order();
        $order->setCreatedAt($date);

        $transaction = $this->createMock(TransactionInterface::class);
        $transaction->expects(static::exactly(2))->method('get')->willReturnCallback([$this, 'getCallback']);
        $transaction->expects(static::once())->method('setTransactionId');
        $transaction->expects(static::any())->method('getOrder')->willReturn($order);
        $transaction->expects(static::any())->method('getInformation')->willReturn('');

        static::assertSame('free_1', $payment->getCode(), 'Pass Payment return the correct code');
        static::assertTrue($payment->isAddableProduct($basket, $product));
        static::assertTrue($payment->isBasketValid($basket));
        static::assertTrue($payment->isRequestValid($transaction));
        static::assertTrue($payment->isCallbackValid($transaction));

        static::assertInstanceOf(Response::class, $payment->handleError($transaction));

        static::assertSame($payment->getOrderReference($transaction), '0001231');

        $payment->applyTransactionId($transaction);
    }

    public function testSendbank(): void
    {
        $date = new \DateTime('1981-11-30', new \DateTimeZone('Europe/Paris'));

        $order = new CheckPaymentTest_Order();
        $order->setCreatedAt($date);

        $router = $this->createMock(RouterInterface::class);
        $router->expects(static::exactly(2))->method('generate')->willReturn('http://foo.bar/ok-url');

        $logger = $this->createMock(LoggerInterface::class);

        $client = $this->createMock(ClientInterface::class);
        $client->expects(static::once())->method('send')->willReturnCallback(static function ($request, $response): void {
            $response->setContent('ok');
        });

        $browser = new Browser($client);
        $payment = new CheckPayment($router, $logger, $browser);

        $response = $payment->sendbank($order);

        static::assertInstanceOf(Response::class, $response);
        static::assertSame(302, $response->getStatusCode());
        static::assertSame('http://foo.bar/ok-url', $response->headers->get('Location'));
        static::assertFalse($response->isCacheable());
    }

    public function testSendConfirmationReceipt(): void
    {
        $order = new CheckPaymentTest_Order();

        $transaction = $this->createMock(TransactionInterface::class);
        $transaction->expects(static::exactly(2))->method('getOrder')->will(static::onConsecutiveCalls(null, $order));

        $router = $this->createMock(RouterInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $browser = new Browser();

        $payment = new CheckPayment($router, $logger, $browser);
        $payment->setCode('free_1');

        // first call : the order is not set
        $response = $payment->sendConfirmationReceipt($transaction);
        static::assertFalse($response, '::sendConfirmationReceipt return false on invalid order');

        // second call : the order is set
        $response = $payment->sendConfirmationReceipt($transaction);
        static::assertInstanceOf(Response::class, $response, '::sendConfirmationReceipt return a Response object');
        static::assertSame('ok', $response->getContent(), '::getContent returns ok');
    }

    public static function getCallback($name)
    {
        if ('reference' === $name) {
            return '0001231';
        }

        if ('transaction_id' === $name) {
            return 1;
        }

        if ('check' === $name) {
            return 'a51e9421db1c028e2ccf47f8999dc902ea6df3ac';
        }
    }
}
