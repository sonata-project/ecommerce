<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Tests\Transformer;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Sonata\Component\Basket\Basket;
use Sonata\Component\Currency\Currency;
use Sonata\Component\Transformer\BasketTransformer;
use Sonata\OrderBundle\Entity\BaseOrder;

class BasketTransformerTest_Order extends BaseOrder
{
    /**
     * @return int the order id
     */
    public function getId()
    {
        // TODO: Implement getId() method.
    }
}

class BasketTransformerTest extends TestCase
{
    /**
     * @return \Sonata\Component\Transformer\BasketTransformer
     */
    public function getBasketTransform()
    {
        $order = new BasketTransformerTest_Order();
        $orderManager = $this->createMock('Sonata\Component\Order\OrderManagerInterface');
        $orderManager->expects($this->any())->method('create')->will($this->returnValue($order));

        $productPool = $this->createMock('Sonata\Component\Product\Pool');

        $logger = $this->createMock(LoggerInterface::class);
        $eventDispatcher = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $basketTransform = new BasketTransformer($orderManager, $productPool, $eventDispatcher, $logger);

        return $basketTransform;
    }

    public function testInvalidCustomer()
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Invalid customer');

        $basket = new Basket();

        $currency = new Currency();
        $currency->setLabel('EUR');
        $basket->setCurrency($currency);

        $this->getBasketTransform()->transformIntoOrder($basket);
    }

    public function testInvalidBillingAddress()
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Invalid billing address');

        $basket = new Basket();
        $customer = $this->createMock('Sonata\Component\Customer\CustomerInterface');

        $basket->setCustomer($customer);

        $currency = new Currency();
        $currency->setLabel('EUR');
        $basket->setCurrency($currency);

        $this->getBasketTransform()->transformIntoOrder($basket);
    }

    public function testInvalidPaymentMethod()
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Invalid payment method');

        $basket = new Basket();
        $customer = $this->createMock('Sonata\Component\Customer\CustomerInterface');
        $billingAddress = $this->createMock('Sonata\Component\Customer\AddressInterface');

        $currency = new Currency();
        $currency->setLabel('EUR');
        $basket->setCurrency($currency);

        $basket->setCustomer($customer);
        $basket->setBillingAddress($billingAddress);

        $this->getBasketTransform()->transformIntoOrder($basket);
    }

    public function testInvalidDeliveryMethod()
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Invalid delivery method');

        $basket = new Basket();
        $customer = $this->createMock('Sonata\Component\Customer\CustomerInterface');
        $billingAddress = $this->createMock('Sonata\Component\Customer\AddressInterface');
        $paymentMethod = $this->createMock('Sonata\Component\Payment\PaymentInterface');

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
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Invalid delivery address');

        $basket = new Basket();
        $customer = $this->createMock('Sonata\Component\Customer\CustomerInterface');
        $billingAddress = $this->createMock('Sonata\Component\Customer\AddressInterface');
        $paymentMethod = $this->createMock('Sonata\Component\Payment\PaymentInterface');
        $deliveryMethod = $this->createMock('Sonata\Component\Delivery\ServiceDeliveryInterface');
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

    public function testOrder()
    {
        $basket = new Basket();
        $customer = $this->createMock('Sonata\Component\Customer\CustomerInterface');
        $billingAddress = $this->createMock('Sonata\Component\Customer\AddressInterface');
        $deliveryMethod = $this->createMock('Sonata\Component\Delivery\ServiceDeliveryInterface');
        $deliveryMethod->expects($this->exactly(2))->method('isAddressRequired')->will($this->returnValue(true));
        $deliveryAddress = $this->createMock('Sonata\Component\Customer\AddressInterface');
        $paymentMethod = $this->createMock('Sonata\Component\Payment\PaymentInterface');

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
