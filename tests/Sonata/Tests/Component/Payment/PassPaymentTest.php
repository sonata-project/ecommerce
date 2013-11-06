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

use Sonata\Component\Payment\PassPayment;
use Buzz\Message\Response;
use Buzz\Browser;
use Sonata\OrderBundle\Entity\BaseOrder;

class PassPaymentTest_Order extends BaseOrder
{
    /**
     * @return integer the order id
     */
    public function getId()
    {
        // TODO: Implement getId() method.
    }

}
class PassPaymentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * useless test ....
     *
     * @return void
     */
    public function testPassPayment()
    {
        $router = $this->getMock('Symfony\Component\Routing\RouterInterface');
        $router->expects($this->exactly(2))->method('generate')->will($this->returnValue('http://foo.bar/ok-url'));

        $client = $this->getMock('Buzz\Client\ClientInterface');

        $browser = new Browser($client);
        $payment = new PassPayment($router, $browser);
        $payment->setCode('free_1');

        $basket = $this->getMock('Sonata\Component\Basket\Basket');
        $product = $this->getMock('Sonata\Component\Product\ProductInterface');

        $transaction = $this->getMock('Sonata\Component\Payment\TransactionInterface');
        $transaction->expects($this->exactly(2))->method('get')->will($this->returnCallback(array($this, 'callback')));
        $transaction->expects($this->once())->method('setTransactionId');

        $date = new \DateTime();
        $date->setTimeStamp(strtotime('30/11/1981'));
        $date->setTimezone(new \DateTimeZone('Europe/Paris'));

        $order = new PassPaymentTest_Order;
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
