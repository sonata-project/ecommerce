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
use Buzz\Message\Response;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Sonata\Component\Payment\CheckPayment;
use Sonata\OrderBundle\Entity\BaseOrder;

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
    public function testPassPayment(): void
    {
        $router = $this->createMock('Symfony\Component\Routing\RouterInterface');
        $logger = $this->createMock(LoggerInterface::class);

        $browser = new Browser();
        $payment = new CheckPayment($router, $logger, $browser);
        $payment->setCode('free_1');

        $basket = $this->createMock('Sonata\Component\Basket\Basket');
        $product = $this->createMock('Sonata\Component\Product\ProductInterface');

        $date = new \DateTime();
        $date->setTimeStamp(strtotime('11/30/1981'));
        $date->setTimezone(new \DateTimeZone('Europe/Paris'));

        $order = new CheckPaymentTest_Order();
        $order->setCreatedAt($date);

        $transaction = $this->createMock('Sonata\Component\Payment\TransactionInterface');
        $transaction->expects($this->exactly(2))->method('get')->will($this->returnCallback([$this, 'callback']));
        $transaction->expects($this->once())->method('setTransactionId');
        $transaction->expects($this->any())->method('getOrder')->will($this->returnValue($order));
        $transaction->expects($this->any())->method('getInformation')->will($this->returnValue(''));

        $this->assertEquals('free_1', $payment->getCode(), 'Pass Payment return the correct code');
        $this->assertTrue($payment->isAddableProduct($basket, $product));
        $this->assertTrue($payment->isBasketValid($basket));
        $this->assertTrue($payment->isRequestValid($transaction));
        $this->assertTrue($payment->isCallbackValid($transaction));

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $payment->handleError($transaction));

        $this->assertEquals($payment->getOrderReference($transaction), '0001231');

        $payment->applyTransactionId($transaction);
    }

    public function testSendbank(): void
    {
        $date = new \DateTime();
        $date->setTimeStamp(strtotime('11/30/1981'));
        $date->setTimezone(new \DateTimeZone('Europe/Paris'));

        $order = new CheckPaymentTest_Order();
        $order->setCreatedAt($date);

        $router = $this->createMock('Symfony\Component\Routing\RouterInterface');
        $router->expects($this->exactly(2))->method('generate')->will($this->returnValue('http://foo.bar/ok-url'));

        $logger = $this->createMock(LoggerInterface::class);

        $client = $this->createMock('Buzz\Client\ClientInterface');
        $client->expects($this->once())->method('send')->will($this->returnCallback(function ($request, $response): void {
            $response->setContent('ok');
        }));

        $browser = new Browser($client);
        $payment = new CheckPayment($router, $logger, $browser);

        $response = $payment->sendbank($order);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('http://foo.bar/ok-url', $response->headers->get('Location'));
        $this->assertFalse($response->isCacheable());
    }

    public function testSendConfirmationReceipt(): void
    {
        $order = new CheckPaymentTest_Order();

        $transaction = $this->createMock('Sonata\Component\Payment\TransactionInterface');
        $transaction->expects($this->exactly(2))->method('getOrder')->will($this->onConsecutiveCalls(null, $order));

        $router = $this->createMock('Symfony\Component\Routing\RouterInterface');
        $logger = $this->createMock(LoggerInterface::class);
        $browser = new Browser();

        $payment = new CheckPayment($router, $logger, $browser);
        $payment->setCode('free_1');

        // first call : the order is not set
        $response = $payment->sendConfirmationReceipt($transaction);
        $this->assertFalse($response, '::sendConfirmationReceipt return false on invalid order');

        // second call : the order is set
        $response = $payment->sendConfirmationReceipt($transaction);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response, '::sendConfirmationReceipt return a Response object');
        $this->assertEquals('ok', $response->getContent(), '::getContent returns ok');
    }

    public static function callback($name)
    {
        if ('reference' == $name) {
            return '0001231';
        }

        if ('transaction_id' == $name) {
            return 1;
        }

        if ('check' == $name) {
            return '0df8a3065a433ffbd907b2e6450199fb25e7902f';
        }
    }
}
