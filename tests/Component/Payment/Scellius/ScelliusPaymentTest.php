<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Tests\Payment\Scellius;

use Sonata\Component\Currency\Currency;
use Sonata\Component\Payment\Scellius\ScelliusPayment;
use Sonata\OrderBundle\Entity\BaseOrder;
use Sonata\Tests\Helpers\PHPUnit_Framework_TestCase;

class ScelliusPaymentTest_Order extends BaseOrder
{
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int the order id
     */
    public function getId()
    {
        return $this->id;
    }

    public function getCurrency()
    {
        if (null === $this->currency) {
            return new Currency();
        }

        return $this->currency;
    }
}
class ScelliusPaymentTest extends PHPUnit_Framework_TestCase
{
    /**
     * useless test ....
     */
    public function testValidPayment()
    {
        $logger = $this->createMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $templating = $this->createMock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
        $router = $this->createMock('Symfony\Component\Routing\RouterInterface');
        $generator = $this->createMock('Sonata\Component\Payment\Scellius\ScelliusTransactionGeneratorInterface');

        $payment = new ScelliusPayment($router, $logger, $templating, $generator, true);
        $payment->setCode('free_1');
        $payment->setOptions([
            'base_folder' => __DIR__,
            'response_command' => 'cat response_ok.txt && echo ',
        ]);

        $basket = $this->createMock('Sonata\Component\Basket\Basket');
        $product = $this->createMock('Sonata\Component\Product\ProductInterface');

        $date = new \DateTime();
        $date->setTimeStamp(strtotime('30/11/1981'));
        $date->setTimezone(new \DateTimeZone('Europe/Paris'));

        $order = new ScelliusPaymentTest_Order();
        $order->setCreatedAt($date);
        $order->setId(2);
        $order->setReference('FR');
        $order->setLocale('es');

        $transaction = $this->createMock('Sonata\Component\Payment\TransactionInterface');
        $transaction->expects($this->any())->method('get')->will($this->returnCallback([$this, 'callback']));
        //        $transaction->expects($this->once())->method('setTransactionId');
        $transaction->expects($this->any())->method('getOrder')->will($this->returnValue($order));
        $transaction->expects($this->any())->method('getCreatedAt')->will($this->returnValue($date));

        $this->assertEquals('free_1', $payment->getCode(), 'Pass Payment return the correct code');
        $this->assertTrue($payment->isAddableProduct($basket, $product));
        $this->assertTrue($payment->isBasketValid($basket));
        $this->assertTrue($payment->isRequestValid($transaction));

        $this->assertTrue($payment->isCallbackValid($transaction));

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $payment->handleError($transaction));
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $payment->sendConfirmationReceipt($transaction));

        //        $response = $payment->sendbank($order);
//
//        $this->assertTrue($response->headers->has('Location'));
//        $this->assertEquals('http://foo.bar/ok-url', $response->headers->get('Location'));
//        $this->assertFalse($response->isCacheable());
//
//        $this->assertEquals($payment->getOrderReference($transaction), '0001231');
//
//        $payment->applyTransactionId($transaction);
    }

    public function testSendConfirmationReceipt()
    {
        $logger = $this->createMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $templating = $this->createMock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
        $router = $this->createMock('Symfony\Component\Routing\RouterInterface');
        $generator = $this->createMock('Sonata\Component\Payment\Scellius\ScelliusTransactionGeneratorInterface');

        $payment = new ScelliusPayment($router, $logger, $templating, $generator, true);
        $payment->setOptions([
            'base_folder' => __DIR__,
            'response_command' => 'cat response_ko.txt && echo ',
        ]);

        $transaction = $this->createMock('Sonata\Component\Payment\TransactionInterface');
        $transaction->expects($this->any())->method('get')->will($this->returnValue('" >> /dev/null'));
        $transaction->expects($this->any())->method('getParameters')->will($this->returnValue([]));
        $transaction->expects($this->any())->method('getOrder')->will($this->returnValue(
            $this->createMock('Sonata\Component\Order\OrderInterface')
        ));

        $this->assertFalse($payment->sendConfirmationReceipt($transaction));

        $payment->setOptions([
            'base_folder' => __DIR__,
            'response_command' => 'cat response_nok.txt && echo ',
        ]);

        $this->assertFalse($payment->sendConfirmationReceipt($transaction));

        $payment->setOptions([
            'base_folder' => __DIR__,
            'response_command' => 'cat response_code_nok.txt && echo ',
        ]);

        $this->assertFalse($payment->sendConfirmationReceipt($transaction));

        $payment->setOptions([
            'base_folder' => __DIR__,
            'response_command' => 'cat response_ok.txt && echo ',
        ]);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $payment->sendConfirmationReceipt($transaction));
    }

    public function testIsCallbackValid()
    {
        $logger = $this->createMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $templating = $this->createMock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
        $router = $this->createMock('Symfony\Component\Routing\RouterInterface');
        $generator = $this->createMock('Sonata\Component\Payment\Scellius\ScelliusTransactionGeneratorInterface');

        $payment = new ScelliusPayment($router, $logger, $templating, $generator, true);

        $order = $this->createMock('Sonata\Component\Order\OrderInterface');
        $order->expects($this->any())->method('getCreatedAt')->will($this->returnValue(new \DateTime()));

        $check = sha1(
            $order->getReference().
            $order->getCreatedAt()->format('m/d/Y:G:i:s').
            $order->getId()
        );

        $transaction = $this->createMock('Sonata\Component\Payment\TransactionInterface');
        $transaction->expects($this->once())->method('getOrder')->will($this->returnValue(null));

        $this->assertFalse($payment->isCallbackValid($transaction));

        $transaction = $this->createMock('Sonata\Component\Payment\TransactionInterface');
        $transaction->expects($this->exactly(2))->method('getOrder')->will($this->returnValue($order));
        $transaction->expects($this->once())->method('get')->will($this->returnValue($check));

        $this->assertTrue($payment->isCallbackValid($transaction));

        $transaction = $this->createMock('Sonata\Component\Payment\TransactionInterface');
        $transaction->expects($this->exactly(2))->method('getOrder')->will($this->returnValue($order));
        $transaction->expects($this->once())->method('get')->will($this->returnValue('untest'));
        $transaction->expects($this->once())->method('setState');
        $transaction->expects($this->once())->method('setStatusCode');
        $transaction->expects($this->once())->method('addInformation');

        $this->assertFalse($payment->isCallbackValid($transaction));
    }

    public function testGetOrderReference()
    {
        $logger = $this->createMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $templating = $this->createMock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
        $router = $this->createMock('Symfony\Component\Routing\RouterInterface');
        $generator = $this->createMock('Sonata\Component\Payment\Scellius\ScelliusTransactionGeneratorInterface');

        $payment = new ScelliusPayment($router, $logger, $templating, $generator, true);

        $transaction = $this->createMock('Sonata\Component\Payment\TransactionInterface');
        $transaction->expects($this->once())->method('get')->will($this->returnValue('reference'));

        $this->assertEquals('reference', $payment->getOrderReference($transaction));
    }

    public function testApplyTransactionId()
    {
        $logger = $this->createMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $templating = $this->createMock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
        $router = $this->createMock('Symfony\Component\Routing\RouterInterface');
        $generator = $this->createMock('Sonata\Component\Payment\Scellius\ScelliusTransactionGeneratorInterface');

        $payment = new ScelliusPayment($router, $logger, $templating, $generator, true);

        $transaction = $this->createMock('Sonata\Component\Payment\TransactionInterface');
        $transaction->expects($this->once())->method('setTransactionId');

        $payment->applyTransactionId($transaction);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testInvalidCurrencySendbankPayment()
    {
        $logger = $this->createMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $templating = $this->createMock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
        $router = $this->createMock('Symfony\Component\Routing\RouterInterface');

        $generator = $this->createMock('Sonata\Component\Payment\Scellius\ScelliusTransactionGeneratorInterface');

        $date = new \DateTime();
        $date->setTimeStamp(strtotime('30/11/1981'));
        $date->setTimezone(new \DateTimeZone('Europe/Paris'));

        $order = new ScelliusPaymentTest_Order();
        $order->setCreatedAt($date);
        $order->setId(2);
        $order->setReference('FR');
        $order->setLocale('es');

        $payment = new ScelliusPayment($router, $logger, $templating, $generator, true);
        $payment->setCode('free_1');
        $payment->setOptions([
            'base_folder' => __DIR__,
            'request_command' => 'cat request_ok.txt && echo ',
        ]);

        $payment->sendbank($order);
    }

    public function testValidSendbankPayment()
    {
        $logger = $this->createMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $templating = $this->createMock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
        $templating->expects($this->once())->method('renderResponse')->will($this->returnCallback([$this, 'callbackValidsendbank']));
        $generator = $this->createMock('Sonata\Component\Payment\Scellius\ScelliusTransactionGeneratorInterface');

        $router = $this->createMock('Symfony\Component\Routing\RouterInterface');

        $date = new \DateTime();
        $date->setTimeStamp(strtotime('30/11/1981'));
        $date->setTimezone(new \DateTimeZone('Europe/Paris'));

        $customer = $this->createMock('Sonata\Component\Customer\CustomerInterface');
        $customer->expects($this->once())->method('getId')->will($this->returnValue(42));
        $customer->expects($this->once())->method('getEmail')->will($this->returnValue('contact@sonata-project.org'));

        $order = new ScelliusPaymentTest_Order();
        $order->setCreatedAt($date);
        $order->setId(2);
        $order->setReference('FR');

        $currency = new Currency();
        $currency->setLabel('EUR');
        $order->setCurrency($currency);
        $order->setCustomer($customer);
        $order->setLocale('es');

        $payment = new ScelliusPayment($router, $logger, $templating, $generator, true);
        $payment->setCode('free_1');
        $payment->setOptions([
            'base_folder' => __DIR__,
            'request_command' => 'cat request_ok.txt && echo ',
        ]);

        $response = $payment->sendbank($order);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
    }

    /**
     * @dataProvider getEncodeStringValues
     */
    public function testEncodeString($data, $expected)
    {
        $logger = $this->createMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $templating = $this->createMock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
        $router = $this->createMock('Symfony\Component\Routing\RouterInterface');
        $generator = $this->createMock('Sonata\Component\Payment\Scellius\ScelliusTransactionGeneratorInterface');

        $payment = new ScelliusPayment($router, $logger, $templating, $generator, true);

        $this->assertEquals($expected, $payment->encodeString($data));
    }

    public static function getEncodeStringValues()
    {
        return [
            ['valid', 'valid'],
            ['!@#$', '!@\#\$'],
            ['foo=bar', 'foo=bar'],
        ];
    }

    public function callbackValidSendbank($template, $params)
    {
        if (!$params['scellius']['valid']) {
            throw new \RuntimeException('Scellius validation should be ok');
        }

        if ($params['scellius']['content'] != '<div>message</div>') {
            throw new \RuntimeException('Invalid scellius html message');
        }

        return new \Symfony\Component\HttpFoundation\Response();
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
            return '56384d4138b4219e554aa3cc781151686064e699';
        }
    }
}
