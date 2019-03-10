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
use Sonata\OrderBundle\Entity\BaseOrder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class CheckPaymentTest_Order extends BaseOrder
{
    /**
     * @return int the order id
     */
    public function getId()
    {
        // TODO: Implement getId() method.
    }
}

class CheckPaymentTest extends TestCase
{
    /**
     * useless test ....
     */
    public function testPassPayment()
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
        $transaction->expects($this->exactly(2))->method('get')->will($this->returnCallback([$this, 'getCallback']));
        $transaction->expects($this->once())->method('setTransactionId');
        $transaction->expects($this->any())->method('getOrder')->will($this->returnValue($order));

        $this->assertSame('free_1', $payment->getCode(), 'Pass Payment return the correct code');
        $this->assertTrue($payment->isAddableProduct($basket, $product));
        $this->assertTrue($payment->isBasketValid($basket));
        $this->assertTrue($payment->isRequestValid($transaction));
        $this->assertTrue($payment->isCallbackValid($transaction));

        $this->assertInstanceOf(Response::class, $payment->handleError($transaction));

        $this->assertSame($payment->getOrderReference($transaction), '0001231');

        $payment->applyTransactionId($transaction);
    }

    public function testSendbank()
    {
        $date = new \DateTime('1981-11-30', new \DateTimeZone('Europe/Paris'));

        $order = new CheckPaymentTest_Order();
        $order->setCreatedAt($date);

        $router = $this->createMock(RouterInterface::class);
        $router->expects($this->exactly(2))->method('generate')->will($this->returnValue('http://foo.bar/ok-url'));

        $logger = $this->createMock(LoggerInterface::class);

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())->method('send')->will($this->returnCallback(function ($request, $response) {
            $response->setContent('ok');
        }));

        $browser = new Browser($client);
        $payment = new CheckPayment($router, $logger, $browser);

        $response = $payment->sendbank($order);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('http://foo.bar/ok-url', $response->headers->get('Location'));
        $this->assertFalse($response->isCacheable());
    }

    public function testSendConfirmationReceipt()
    {
        $order = new CheckPaymentTest_Order();

        $transaction = $this->createMock(TransactionInterface::class);
        $transaction->expects($this->exactly(2))->method('getOrder')->will($this->onConsecutiveCalls(null, $order));

        $router = $this->createMock(RouterInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $browser = new Browser();

        $payment = new CheckPayment($router, $logger, $browser);
        $payment->setCode('free_1');

        // first call : the order is not set
        $response = $payment->sendConfirmationReceipt($transaction);
        $this->assertFalse($response, '::sendConfirmationReceipt return false on invalid order');

        // second call : the order is set
        $response = $payment->sendConfirmationReceipt($transaction);
        $this->assertInstanceOf(Response::class, $response, '::sendConfirmationReceipt return a Response object');
        $this->assertSame('ok', $response->getContent(), '::getContent returns ok');
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
