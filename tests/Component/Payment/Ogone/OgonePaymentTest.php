<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\Component\Payment\Ogone;

use Sonata\OrderBundle\Entity\BaseOrder;
use Sonata\Component\Payment\Ogone\OgonePayment;
use Sonata\Component\Currency\Currency;

class OgonePaymentTest_Order extends BaseOrder
{

    public function setId($id)
    {
        $this->id = $id;
    }
    /**
     * @return integer the order id
     */
    public function getId()
    {
        return $this->id;
    }

}
class OgonePaymentTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @return void
     */
    public function testValidPayment()
    {
        $logger     = $this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $templating = $this->getMock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
        $router     = $this->getMock('Symfony\Component\Routing\RouterInterface');
        $router->expects($this->once())->method('generate')->will($this->returnValue('http://www.google.com'));

        $payment = new OgonePayment($router, $logger, $templating, true);
        $payment->setCode('ogone_1');
        $payment->setOptions(array(
            'url_return_ok'    => "sonata_payment_confirmation",
            'url_return_ko' => "",
            'url_callback' => "",
            'template' => "",
            'form_url' => "",
            'sha_key' => "",
            'sha-out_key' => "",
            'pspid' => "",
            'home_url' => "",
            'catalog_url' => "",
        ));

        $basket = $this->getMock('Sonata\Component\Basket\Basket');
        $product = $this->getMock('Sonata\Component\Product\ProductInterface');

        $date = new \DateTime();
        $date->setTimeStamp(strtotime('30/11/1981'));
        $date->setTimezone(new \DateTimeZone('Europe/Paris'));

        $order = new OgonePaymentTest_Order();
        $order->setCreatedAt($date);
        $order->setId(2);
        $order->setReference('FR');
        $order->setLocale('es');

        $transaction = $this->getMock('Sonata\Component\Payment\TransactionInterface');
        $transaction->expects($this->any())->method('get')->will($this->returnCallback(array($this, 'callback')));
//        $transaction->expects($this->once())->method('setTransactionId');
        $transaction->expects($this->any())->method('getOrder')->will($this->returnValue($order));
        $transaction->expects($this->any())->method('getCreatedAt')->will($this->returnValue($date));

        $this->assertEquals('ogone_1', $payment->getCode(), 'Ogone Payment return the correct code');
        $this->assertTrue($payment->isAddableProduct($basket, $product));
        $this->assertTrue($payment->isBasketValid($basket));
        $this->assertTrue($payment->isRequestValid($transaction));

        $this->assertTrue($payment->isCallbackValid($transaction));

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $payment->handleError($transaction));
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $payment->sendConfirmationReceipt($transaction));
    }

    public function testValidSendbankPayment()
    {
        $logger     = $this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $templating = $this->getMock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
        $templating->expects($this->once())->method('renderResponse')->will($this->returnCallback(array($this, 'callbackValidsendbank')));

        $router     = $this->getMock('Symfony\Component\Routing\RouterInterface');

        $date = new \DateTime();
        $date->setTimeStamp(strtotime('30/11/1981'));
        $date->setTimezone(new \DateTimeZone('Europe/Paris'));

        $customer   = $this->getMock('Sonata\Component\Customer\CustomerInterface');

        $order = new OgonePaymentTest_Order();
        $order->setCreatedAt($date);
        $order->setId(2);
        $order->setReference('FR');

        $currency = new Currency();
        $currency->setLabel('EUR');
        $order->setCurrency($currency);
        $order->setCustomer($customer);
        $order->setLocale('es');

        $payment = new OgonePayment($router, $logger, $templating, true);
        $payment->setCode('ogone_1');
        $payment->setOptions(array(
            'url_return_ok'    => "",
            'url_return_ko' => "",
            'url_callback' => "",
            'template' => "",
            'form_url' => "",
            'sha_key' => "",
            'sha-out_key' => "",
            'pspid' => "",
            'home_url' => "",
            'catalog_url' => "",
        ));

        $response = $payment->sendbank($order);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
    }

    /**
     * @dataProvider getEncodeStringValues
     */
    public function testEncodeString($data, $expected)
    {
        $logger     = $this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $templating = $this->getMock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
        $router     = $this->getMock('Symfony\Component\Routing\RouterInterface');

        $payment = new OgonePayment($router, $logger, $templating, true);
        $payment->setCode('ogone_1');
        $payment->setOptions(array(
                'url_return_ok'    => "",
                'url_return_ko' => "",
                'url_callback' => "",
                'template' => "",
                'form_url' => "",
                'sha_key' => "",
                'sha-out_key' => "",
                'pspid' => "",
                'home_url' => "",
                'catalog_url' => "",
        ));

        $this->assertEquals($expected, $payment->encodeString($data));
    }

    public static function getEncodeStringValues()
    {
        return array(
            array('valid', 'valid'),
            array('!@#$', '!@#$'),
            array('foo=bar', 'foo=bar'),
        );
    }

    public function callbackValidsendbank($template, $params)
    {
        if (!$params['shasign']) {
            throw new \RuntimeException('Ogone validation should be ok');
        }

        if ($params['fields']['orderId'] != 'FR') {
            throw new \RuntimeException('Invalid ogone orderId');
        }

        return new \Symfony\Component\HttpFoundation\Response();
    }

    public static function callback($name)
    {
        $params = array(
                'orderID' => 'FR',
                'currency' => null,
                'amount' => 'amount',
                'PM' => 'PM',
                'ACCEPTANCE' => 'ACCEPTANCE',
                'STATUS' => 'STATUS',
                'CARDNO' => 'CARDNO',
                'ED' => 'ED',
                'CN' => 'CN',
                'TRXDATE' => 'TRXDATE',
                'PAYID' => 'PAYID',
                'NCERROR' => 'NCERROR',
                'BRAND' => 'BRAND',
                'IP' => 'IP',
        );

        if (strcasecmp('shasign', $name) === 0) {
            uksort($params, 'strcasecmp');

            $shaKey = "";

            $shasignStr = "";
            foreach ($params as $key => $param) {
                if (null !== $param && "" !== $param) {
                    $shasignStr .= strtoupper($key)."=".$param.$shaKey;
                }
            }

            return strtoupper(sha1($shasignStr));
        }

        $params['check'] = "56384d4138b4219e554aa3cc781151686064e699";

        return $params[$name];
    }

    public function testIsCallbackValid()
    {
        $logger     = $this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $templating = $this->getMock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
        $router     = $this->getMock('Symfony\Component\Routing\RouterInterface');

        $payment = new OgonePayment($router, $logger, $templating, true);

        $order = $this->getMock('Sonata\Component\Order\OrderInterface');
        $order->expects($this->any())->method('getCreatedAt')->will($this->returnValue(new \DateTime()));

        $check = sha1(
            $order->getReference().
            $order->getCreatedAt()->format("m/d/Y:G:i:s").
            $order->getId()
        );

        $transaction = $this->getMock('Sonata\Component\Payment\TransactionInterface');
        $transaction->expects($this->once())->method('getOrder')->will($this->returnValue(null));

        $this->assertFalse($payment->isCallbackValid($transaction));

        $transaction = $this->getMock('Sonata\Component\Payment\TransactionInterface');
        $transaction->expects($this->exactly(2))->method('getOrder')->will($this->returnValue($order));
        $transaction->expects($this->once())->method('get')->will($this->returnValue($check));

        $this->assertTrue($payment->isCallbackValid($transaction));

        $transaction = $this->getMock('Sonata\Component\Payment\TransactionInterface');
        $transaction->expects($this->exactly(2))->method('getOrder')->will($this->returnValue($order));
        $transaction->expects($this->once())->method('get')->will($this->returnValue('untest'));
        $transaction->expects($this->once())->method('setState');
        $transaction->expects($this->once())->method('setStatusCode');
        $transaction->expects($this->once())->method('addInformation');

        $this->assertFalse($payment->isCallbackValid($transaction));
    }

    public function testGetOrderReference()
    {
        $logger     = $this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $templating = $this->getMock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
        $router     = $this->getMock('Symfony\Component\Routing\RouterInterface');

        $payment = new OgonePayment($router, $logger, $templating, true);

        $transaction = $this->getMock('Sonata\Component\Payment\TransactionInterface');
        $transaction->expects($this->once())->method('get')->will($this->returnValue("reference"));

        $this->assertEquals("reference", $payment->getOrderReference($transaction));
    }

    public function testApplyTransactionId()
    {
        $logger     = $this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $templating = $this->getMock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
        $router     = $this->getMock('Symfony\Component\Routing\RouterInterface');

        $payment = new OgonePayment($router, $logger, $templating, true);

        $transaction = $this->getMock('Sonata\Component\Payment\TransactionInterface');
        $transaction->expects($this->once())->method('setTransactionId');

        $payment->applyTransactionId($transaction);
    }

}
