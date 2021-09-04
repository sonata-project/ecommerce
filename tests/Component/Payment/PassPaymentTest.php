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
use Sonata\Component\Basket\Basket;
use Sonata\Component\Payment\PassPayment;
use Sonata\Component\Payment\TransactionInterface;
use Sonata\Component\Product\ProductInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class PassPaymentTest extends TestCase
{
    /**
     * useless test ....
     */
    public function testPassPayment(): void
    {
        $router = $this->createMock(RouterInterface::class);
        $router->expects(static::exactly(2))->method('generate')->willReturn('http://foo.bar/ok-url');

        $client = $this->createMock(ClientInterface::class);
        $client->expects(static::once())->method('send')->willReturnCallback(static function ($request, $response): void {
            $response->setContent('ok');
        });

        $browser = new Browser($client);
        $payment = new PassPayment($router, $browser);
        $payment->setCode('free_1');

        $basket = $this->createMock(Basket::class);
        $product = $this->createMock(ProductInterface::class);

        $transaction = $this->createMock(TransactionInterface::class);
        $transaction->expects(static::exactly(2))->method('get')->willReturnCallback([$this, 'getCallback']);
        $transaction->expects(static::once())->method('setTransactionId');

        $date = new \DateTime('1981-11-30', new \DateTimeZone('Europe/Paris'));

        $order = new PassPaymentTest_Order();
        $order->setCreatedAt($date);

        static::assertSame('free_1', $payment->getCode(), 'Pass Payment return the correct code');
        static::assertTrue($payment->isAddableProduct($basket, $product));
        static::assertTrue($payment->isBasketValid($basket));
        static::assertTrue($payment->isRequestValid($transaction));

        static::assertFalse($payment->isCallbackValid($transaction));
        static::assertFalse($payment->sendConfirmationReceipt($transaction));

        $transaction->expects(static::any())->method('getOrder')->willReturn($order);

        static::assertTrue($payment->isCallbackValid($transaction));
        static::assertInstanceOf(Response::class, $payment->handleError($transaction));

        static::assertInstanceOf(Response::class, $payment->sendConfirmationReceipt($transaction));

        $response = $payment->sendbank($order);

        static::assertTrue($response->headers->has('Location'));
        static::assertSame('http://foo.bar/ok-url', $response->headers->get('Location'));
        static::assertFalse($response->isCacheable());

        static::assertSame($payment->getOrderReference($transaction), '0001231');

        $payment->applyTransactionId($transaction);
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
