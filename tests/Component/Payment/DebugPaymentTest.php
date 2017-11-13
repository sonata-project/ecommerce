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
use PHPUnit\Framework\TestCase;
use Sonata\Component\Payment\Debug\DebugPayment;
use Sonata\Component\Payment\TransactionInterface;
use Sonata\OrderBundle\Entity\BaseOrder;
use Sonata\PaymentBundle\Entity\BaseTransaction;

class DebugPaymentTest_Order extends BaseOrder
{
    public function getId()
    {
    }
}

class DebugPaymentTest_Transaction extends BaseTransaction
{
    public function getId()
    {
    }
}

class DebugPaymentTest extends TestCase
{
    public function testDebugPayment()
    {
        $payment = $this->getDebugPayment();

        $order = $this->getOrder();

        $transaction = $this->getTransactionManager()->create();
        $transaction->setPaymentCode($payment->getCode());
        $transaction->setOrder($order);

        /*
         * Payment refused
         */
        $transaction->setParameters(['action' => 'refuse']);
        $payment->sendConfirmationReceipt($transaction);

        $this->assertEquals(TransactionInterface::STATE_KO, $transaction->getState());
        $this->assertEquals(TransactionInterface::STATUS_ERROR_VALIDATION, $transaction->getStatusCode());

        /*
         * Payment accepted
         */
        $transaction->setParameters(['action' => 'accept']);
        $payment->sendConfirmationReceipt($transaction);

        $this->assertEquals(TransactionInterface::STATE_OK, $transaction->getState());
        $this->assertEquals(TransactionInterface::STATUS_VALIDATED, $transaction->getStatusCode());
    }

    /**
     * @return \Sonata\Component\Order\OrderInterface
     */
    public function getOrder()
    {
        $date = new \DateTime();
        $date->setTimeStamp(strtotime('30/11/1981'));
        $date->setTimezone(new \DateTimeZone('Europe/Paris'));

        $order = new DebugPaymentTest_Order();
        $order->setCreatedAt($date);

        return $order;
    }

    /**
     * @return \Sonata\Component\Payment\TransactionManagerInterface
     */
    protected function getTransactionManager()
    {
        $transactionManager = $this->createMock('Sonata\Component\Payment\TransactionManagerInterface');

        $transactionManager->expects($this->once())
            ->method('create')
            ->will($this->returnValue(new DebugPaymentTest_Transaction()));

        return $transactionManager;
    }

    /**
     * @return \Sonata\Component\Payment\Debug\DebugPayment
     */
    protected function getDebugPayment()
    {
        $router = $this->createMock('Symfony\Component\Routing\RouterInterface');

        $client = $this->createMock('Buzz\Client\ClientInterface');

        $browser = new Browser($client);

        $payment = new DebugPayment($router, $browser);

        return $payment;
    }
}
