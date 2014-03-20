<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\Component\Transformer;

use Sonata\Component\Transformer\BasketTransformer;
use Sonata\Component\Basket\Basket;
use Sonata\OrderBundle\Entity\BaseOrder;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Sonata\Component\Currency\Currency;

class BasketTransformerTest_Order extends BaseOrder
{
    /**
     * @return integer the order id
     */
    public function getId()
    {
        // TODO: Implement getId() method.
    }
}

class BasketTransformerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return \Sonata\Component\Transformer\BasketTransformer
     */
    public function getBasketTransform()
    {
        $order = new BasketTransformerTest_Order;
        $orderManager = $this->getMock('Sonata\Component\Order\OrderManagerInterface');
        $orderManager->expects($this->any())->method('create')->will($this->returnValue($order));

        $productPool = $this->getMock('Sonata\Component\Product\Pool');

        $logger = $this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $eventDispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $basketTransform = new BasketTransformer($orderManager, $productPool, $eventDispatcher, $logger);

        return $basketTransform;
    }

    public function testInvalidCustomer()
    {
        $this->setExpectedException('RuntimeException', 'Invalid customer');

        $basket = new Basket;

        $currency = new Currency();
        $currency->setLabel('EUR');
        $basket->setCurrency($currency);

        $this->getBasketTransform()->transformIntoOrder($basket);
    }

    public function testInvalidBillingAddress()
    {
        $this->setExpectedException('RuntimeException', 'Invalid billing address');

        $basket   = new Basket;
        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');

        $basket->setCustomer($customer);

        $currency = new Currency();
        $currency->setLabel('EUR');
        $basket->setCurrency($currency);

        $this->getBasketTransform()->transformIntoOrder($basket);
    }

    public function testInvalidPaymentMethod()
    {
        $this->setExpectedException('RuntimeException', 'Invalid payment method');

        $basket   = new Basket;
        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');
        $billingAddress = $this->getMock('Sonata\Component\Customer\AddressInterface');

        $currency = new Currency();
        $currency->setLabel('EUR');
        $basket->setCurrency($currency);

        $basket->setCustomer($customer);
        $basket->setBillingAddress($billingAddress);

        $this->getBasketTransform()->transformIntoOrder($basket);
    }

    public function testInvalidDeliveryMethod()
    {
        $this->setExpectedException('RuntimeException', 'Invalid delivery method');

        $basket   = new Basket;
        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');
        $billingAddress = $this->getMock('Sonata\Component\Customer\AddressInterface');
        $paymentMethod = $this->getMock('Sonata\Component\Payment\PaymentInterface');

        $basket->setCustomer($customer);
        $basket->setBillingAddress($billingAddress);
        $basket->setPaymentMethod($paymentMethod);

        $currency = new Currency();
        $currency->setLabel('EUR');
        $basket->setCurrency($currency);

        $this->getBasketTransform()->transformIntoOrder($basket);
    }

    public function testInvalidDeliveryAddress()
    {
        $this->setExpectedException('RuntimeException', 'Invalid delivery address');

        $basket   = new Basket;
        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');
        $billingAddress = $this->getMock('Sonata\Component\Customer\AddressInterface');
        $paymentMethod = $this->getMock('Sonata\Component\Payment\PaymentInterface');
        $deliveryMethod = $this->getMock('Sonata\Component\Delivery\ServiceDeliveryInterface');
        $deliveryMethod->expects($this->once())->method('isAddressRequired')->will($this->returnValue(true));

        $basket->setCustomer($customer);
        $basket->setBillingAddress($billingAddress);
        $basket->setDeliveryMethod($deliveryMethod);
        $basket->setPaymentMethod($paymentMethod);

        $currency = new Currency();
        $currency->setLabel('EUR');
        $basket->setCurrency($currency);

        $this->getBasketTransform()->transformIntoOrder($basket);
    }

    /**
     * @return void
     */
    public function testOrder()
    {

        $basket   = new Basket;
        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');
        $billingAddress = $this->getMock('Sonata\Component\Customer\AddressInterface');
        $deliveryMethod = $this->getMock('Sonata\Component\Delivery\ServiceDeliveryInterface');
        $deliveryMethod->expects($this->exactly(2))->method('isAddressRequired')->will($this->returnValue(true));
        $deliveryAddress = $this->getMock('Sonata\Component\Customer\AddressInterface');
        $paymentMethod = $this->getMock('Sonata\Component\Payment\PaymentInterface');

        $basket->setCustomer($customer);
        $basket->setBillingAddress($billingAddress);
        $basket->setDeliveryMethod($deliveryMethod);
        $basket->setDeliveryAddress($deliveryAddress);
        $basket->setPaymentMethod($paymentMethod);

        $currency = new Currency();
        $currency->setLabel('EUR');
        $basket->setCurrency($currency);

        $order = $this->getBasketTransform()->transformIntoOrder($basket);

        $this->assertInstanceOf('Sonata\Component\Order\OrderInterface', $order, '::transformIntoOrder() returns an OrderInstance object');
    }
}
