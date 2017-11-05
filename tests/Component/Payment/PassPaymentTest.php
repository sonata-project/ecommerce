<?php

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
use Sonata\Component\Payment\PassPayment;
use Sonata\OrderBundle\Entity\BaseOrder;
use Sonata\Tests\Helpers\PHPUnit_Framework_TestCase;

class PassPaymentTest_Order extends BaseOrder
{
    /**
     * @return int the order id
     */
    public function getId()
    {
        // TODO: Implement getId() method.
    }
}
class PassPaymentTest extends PHPUnit_Framework_TestCase
{
    /**
     * useless test ....
     */
    public function testPassPayment()
    {
        $router = $this->createMock('Symfony\Component\Routing\RouterInterface');
        $router->expects($this->exactly(2))->method('generate')->will($this->returnValue('http://foo.bar/ok-url'));

        $client = $this->createMock('Buzz\Client\ClientInterface');

        $browser = new Browser($client);
        $payment = new PassPayment($router, $browser);
        $payment->setCode('free_1');

        $basket = $this->createMock('Sonata\Component\Basket\Basket');
        $product = $this->createMock('Sonata\Component\Product\ProductInterface');

        $transaction = $this->createMock('Sonata\Component\Payment\TransactionInterface');
        $transaction->expects($this->exactly(2))->method('get')->will($this->returnCallback([$this, 'callback']));
        $transaction->expects($this->once())->method('setTransactionId');

        $date = new \DateTime();
        $date->setTimeStamp(strtotime('30/11/1981'));
        $date->setTimezone(new \DateTimeZone('Europe/Paris'));

        $order = new PassPaymentTest_Order();
        $order->setCreatedAt($date);

        $this->assertEquals('free_1', $payment->getCode(), 'Pass Payment return the correct code');
        $this->assertTrue($payment->isAddableProduct($basket, $product));
        $this->assertTrue($payment->isBasketValid($basket));
        $this->assertTrue($payment->isRequestValid($transaction));

        $this->assertFalse($payment->isCallbackValid($transaction));
        $this->assertFalse($payment->sendConfirmationReceipt($transaction));

        $transaction->expects($this->any())->method('getOrder')->will($this->returnValue($order));

        $this->assertTrue($payment->isCallbackValid($transaction));
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $payment->handleError($transaction));

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $payment->sendConfirmationReceipt($transaction));

        $response = $payment->sendbank($order);

        $this->assertTrue($response->headers->has('Location'));
        $this->assertEquals('http://foo.bar/ok-url', $response->headers->get('Location'));
        $this->assertFalse($response->isCacheable());

        $this->assertEquals($payment->getOrderReference($transaction), '0001231');

        $payment->applyTransactionId($transaction);
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
            return '1d4b8187e3b9dbad8336b253176ba3284760757b';
        }
    }
}
