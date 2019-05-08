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

namespace Sonata\Component\Tests\Transformer;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Sonata\Component\Basket\Basket;
use Sonata\Component\Currency\Currency;
use Sonata\Component\Customer\AddressInterface;
use Sonata\Component\Customer\CustomerInterface;
use Sonata\Component\Delivery\ServiceDeliveryInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Order\OrderManagerInterface;
use Sonata\Component\Payment\PaymentInterface;
use Sonata\Component\Product\Pool;
use Sonata\Component\Transformer\BasketTransformer;
use Sonata\OrderBundle\Entity\BaseOrder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
        $orderManager = $this->createMock(OrderManagerInterface::class);
        $orderManager->expects($this->any())->method('create')->willReturn($order);

        $productPool = $this->createMock(Pool::class);

        $logger = $this->createMock(LoggerInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $basketTransform = new BasketTransformer($orderManager, $productPool, $eventDispatcher, $logger);

        return $basketTransform;
    }

    public function testInvalidCustomer(): void
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Invalid customer');

        $basket = new Basket();

        $currency = new Currency();
        $currency->setLabel('EUR');
        $basket->setCurrency($currency);

        $this->getBasketTransform()->transformIntoOrder($basket);
    }

    public function testInvalidBillingAddress(): void
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Invalid billing address');

        $basket = new Basket();
        $customer = $this->createMock(CustomerInterface::class);

        $basket->setCustomer($customer);

        $currency = new Currency();
        $currency->setLabel('EUR');
        $basket->setCurrency($currency);

        $this->getBasketTransform()->transformIntoOrder($basket);
    }

    public function testInvalidPaymentMethod(): void
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Invalid payment method');

        $basket = new Basket();
        $customer = $this->createMock(CustomerInterface::class);
        $billingAddress = $this->createMock(AddressInterface::class);

        $currency = new Currency();
        $currency->setLabel('EUR');
        $basket->setCurrency($currency);

        $basket->setCustomer($customer);
        $basket->setBillingAddress($billingAddress);

        $this->getBasketTransform()->transformIntoOrder($basket);
    }

    public function testInvalidDeliveryMethod(): void
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Invalid delivery method');

        $basket = new Basket();
        $customer = $this->createMock(CustomerInterface::class);
        $billingAddress = $this->createMock(AddressInterface::class);
        $paymentMethod = $this->createMock(PaymentInterface::class);

        $basket->setCustomer($customer);
        $basket->setBillingAddress($billingAddress);
        $basket->setPaymentMethod($paymentMethod);

        $currency = new Currency();
        $currency->setLabel('EUR');
        $basket->setCurrency($currency);

        $this->getBasketTransform()->transformIntoOrder($basket);
    }

    public function testInvalidDeliveryAddress(): void
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Invalid delivery address');

        $basket = new Basket();
        $customer = $this->createMock(CustomerInterface::class);
        $billingAddress = $this->createMock(AddressInterface::class);
        $paymentMethod = $this->createMock(PaymentInterface::class);
        $deliveryMethod = $this->createMock(ServiceDeliveryInterface::class);
        $deliveryMethod->expects($this->once())->method('isAddressRequired')->willReturn(true);

        $basket->setCustomer($customer);
        $basket->setBillingAddress($billingAddress);
        $basket->setDeliveryMethod($deliveryMethod);
        $basket->setPaymentMethod($paymentMethod);

        $currency = new Currency();
        $currency->setLabel('EUR');
        $basket->setCurrency($currency);

        $this->getBasketTransform()->transformIntoOrder($basket);
    }

    public function testOrder(): void
    {
        $basket = new Basket();
        $customer = $this->createMock(CustomerInterface::class);
        $billingAddress = $this->createMock(AddressInterface::class);
        $deliveryMethod = $this->createMock(ServiceDeliveryInterface::class);
        $deliveryMethod->expects($this->exactly(2))->method('isAddressRequired')->willReturn(true);
        $deliveryAddress = $this->createMock(AddressInterface::class);
        $paymentMethod = $this->createMock(PaymentInterface::class);

        $basket->setCustomer($customer);
        $basket->setBillingAddress($billingAddress);
        $basket->setDeliveryMethod($deliveryMethod);
        $basket->setDeliveryAddress($deliveryAddress);
        $basket->setPaymentMethod($paymentMethod);

        $currency = new Currency();
        $currency->setLabel('EUR');
        $basket->setCurrency($currency);

        $order = $this->getBasketTransform()->transformIntoOrder($basket);

        $this->assertInstanceOf(OrderInterface::class, $order, '::transformIntoOrder() returns an OrderInstance object');
    }
}
