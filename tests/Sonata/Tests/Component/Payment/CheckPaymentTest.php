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

use Sonata\Component\Payment\CheckPayment;

use Buzz\Message\Response;
use Buzz\Message\Request;
use Buzz\Browser;
use Buzz\Client\ClientInterface;
use Sonata\OrderBundle\Entity\BaseOrder;

class CheckPaymentTest_Order extends BaseOrder
{
    /**
     * @return integer the order id
     */
    public function getId()
    {
        // TODO: Implement getId() method.
    }
}

class CheckPaymentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * useless test ....
     *
     * @return void
     */
    public function testPassPayment()
    {
        $router = $this->getMock('Symfony\Component\Routing\RouterInterface');
        $logger = $this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface');

        $browser = new Browser();
        $payment = new CheckPayment($router, $logger, $browser);
        $payment->setCode('free_1');

        $basket = $this->getMock('Sonata\Component\Basket\Basket');
        $product = $this->getMock('Sonata\Component\Product\ProductInterface');

        $date = new \DateTime();
        $date->setTimeStamp(strtotime('30/11/1981'));
        $date->setTimezone(new \DateTimeZone('Europe/Paris'));

        $order = new CheckPaymentTest_Order;
        $order->setCreatedAt($date);

        $transaction = $this->getMock('Sonata\Component\Payment\TransactionInterface');
        $transaction->expects($this->exactly(2))->method('get')->will($this->returnCallback(array($this, 'callback')));
        $transaction->expects($this->once())->method('setTransactionId');
        $transaction->expects($this->any())->method('getOrder')->will($this->returnValue($order));

        $this->assertEquals('free_1', $payment->getCode(), 'Pass Payment return the correct code');
        $this->assertTrue($payment->isAddableProduct($basket, $product));
        $this->assertTrue($payment->isBasketValid($basket));
        $this->assertTrue($payment->isRequestValid($transaction));
        $this->assertTrue($payment->isCallbackValid($transaction));

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $payment->handleError($transaction));

        $this->assertEquals($payment->getOrderReference($transaction), '0001231');

        $payment->applyTransactionId($transaction);
    }

    public function testSendbank()
    {
        $date = new \DateTime();
        $date->setTimeStamp(strtotime('30/11/1981'));
        $date->setTimezone(new \DateTimeZone('Europe/Paris'));

        $order = new CheckPaymentTest_Order;
        $order->setCreatedAt($date);

        $router = $this->getMock('Symfony\Component\Routing\RouterInterface');
        $router->expects($this->exactly(2))->method('generate')->will($this->returnValue('http://foo.bar/ok-url'));

        $logger = $this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface');

        $client = $this->getMock('Buzz\Client\ClientInterface');
        $client->expects($this->once())->method('send')->will($this->returnCallback(function ($request, $response) {
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

    public function testSendConfirmationReceipt()
    {
        $order = new CheckPaymentTest_Order;

        $transaction = $this->getMock('Sonata\Component\Payment\TransactionInterface');
        $transaction->expects($this->exactly(2))->method('getOrder')->will($this->onConsecutiveCalls(null, $order));

        $router = $this->getMock('Symfony\Component\Routing\RouterInterface');
        $logger = $this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
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
        if ($name == 'reference') {
            return '0001231';
        }

        if ($name == 'transaction_id') {
            return 1;
        }

        if ($name == 'check') {
            return '1d4b8187e3b9dbad8336b253176ba3284760757b';
        }
    }
}
