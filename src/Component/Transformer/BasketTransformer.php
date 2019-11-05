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

namespace Sonata\Component\Transformer;

use Psr\Log\LoggerInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Customer\AddressInterface;
use Sonata\Component\Customer\CustomerInterface;
use Sonata\Component\Delivery\ServiceDeliveryInterface;
use Sonata\Component\Event\BasketTransformEvent;
use Sonata\Component\Event\OrderTransformEvent;
use Sonata\Component\Event\TransformerEvents;
use Sonata\Component\Order\OrderElementInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Order\OrderManagerInterface;
use Sonata\Component\Payment\PaymentInterface;
use Sonata\Component\Payment\TransactionInterface;
use Sonata\Component\Product\Pool as ProductPool;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BasketTransformer extends BaseTransformer
{
    /**
     * @var OrderManagerInterface
     */
    protected $orderManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ProductPool
     */
    protected $productPool;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(OrderManagerInterface $orderManager, ProductPool $productPool, EventDispatcherInterface $eventDispatcher, LoggerInterface $logger = null)
    {
        $this->productPool = $productPool;
        $this->orderManager = $orderManager;
        $this->logger = $logger;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * transform a basket into order.
     *
     * @param \Sonata\Component\Basket\BasketInterface|null $basket
     *
     * @throws \RuntimeException
     *
     * @return \Sonata\Component\Order\OrderInterface|null
     */
    public function transformIntoOrder(BasketInterface $basket)
    {
        $event = new BasketTransformEvent($basket);
        $this->eventDispatcher->dispatch(TransformerEvents::PRE_BASKET_TO_ORDER_TRANSFORM, $event);

        // Customer
        $customer = $basket->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            if ($this->getLogger()) {
                $this->getLogger()->emergency('[Sonata\Component\Payment\Transform\Basket::transform] the customer is not valid');
            }

            throw new \RuntimeException('Invalid customer');
        }

        // Billing
        $billingAddress = $basket->getBillingAddress();

        if (!$billingAddress instanceof AddressInterface) {
            if ($this->getLogger()) {
                $this->getLogger()->emergency('[Sonata\Component\Payment\Transform\Basket::transform] the billing address is not valid');
            }

            throw new \RuntimeException('Invalid billing address');
        }

        $paymentMethod = $basket->getPaymentMethod();
        if (!$paymentMethod instanceof PaymentInterface) {
            if ($this->getLogger()) {
                $this->getLogger()->emergency('[Sonata\Component\Payment\PaymentInterface::transform] the payment method is not valid');
            }

            throw new \RuntimeException('Invalid payment method');
        }

        // Shipping
        $deliveryMethod = $basket->getDeliveryMethod();
        if (!$deliveryMethod instanceof ServiceDeliveryInterface) {
            if ($this->getLogger()) {
                $this->getLogger()->emergency('[Sonata\Component\Delivery\ServiceDeliveryInterface::transform] the delivery method is not valid');
            }

            throw new \RuntimeException('Invalid delivery method');
        }

        $deliveryAddress = $basket->getDeliveryAddress();
        if ($deliveryMethod->isAddressRequired() && !$deliveryAddress instanceof AddressInterface) {
            if ($this->getLogger()) {
                $this->getLogger()->emergency('[Sonata\Component\Delivery\ServiceDeliveryInterface::transform] the shipping address is not valid');
            }

            throw new \RuntimeException('Invalid delivery address');
        }

        // add a custom class_instance for testing purpose.
        $order = $this->orderManager->create();

        $order->setCustomer($customer);
        $order->setUsername($customer->getFullname());
        $order->setLocale($customer->getLocale());

        if ($deliveryMethod->isAddressRequired()) {
            $order->setShippingAddress1($deliveryAddress->getAddress1());
            $order->setShippingAddress2($deliveryAddress->getAddress2());
            $order->setShippingAddress3($deliveryAddress->getAddress3());
            $order->setShippingPostcode($deliveryAddress->getPostcode());
            $order->setShippingCity($deliveryAddress->getCity());
            $order->setShippingCountryCode($deliveryAddress->getCountryCode());
            $order->setShippingName($deliveryAddress->getFirstname().' '.$deliveryAddress->getLastname());
            $order->setShippingPhone($deliveryAddress->getPhone());
        }

        $order->setBillingAddress1($billingAddress->getAddress1());
        $order->setBillingAddress2($billingAddress->getAddress2());
        $order->setBillingAddress3($billingAddress->getAddress3());
        $order->setBillingPostcode($billingAddress->getPostcode());
        $order->setBillingCity($billingAddress->getCity());
        $order->setBillingCountryCode($billingAddress->getCountryCode());
        $order->setBillingName($billingAddress->getFirstname().' '.$billingAddress->getLastname());
        $order->setBillingPhone($billingAddress->getPhone());

        $order->setTotalExcl($basket->getTotal());
        $order->setTotalInc($basket->getTotal(true));

        $order->setDeliveryVat($basket->getDeliveryVat());
        $order->setDeliveryCost($basket->getDeliveryPrice(true));
        $order->setDeliveryMethod($basket->getDeliveryMethod()->getCode());
        $order->setDeliveryStatus(ServiceDeliveryInterface::STATUS_OPEN);

        $order->setCreatedAt(new \DateTime());

        $order->setCurrency($basket->getCurrency());

        $order->setStatus(OrderInterface::STATUS_OPEN);

        $order->setPaymentStatus(TransactionInterface::STATUS_OPEN);
        $order->setPaymentMethod($basket->getPaymentMethod()->getCode());

        foreach ($basket->getBasketElements() as $basketElement) {
            $orderElement = $basketElement->getProductProvider()->createOrderElement($basketElement);

            if (!$orderElement instanceof OrderElementInterface) {
                continue;
            }

            $order->addOrderElement($orderElement);
        }

        $event = new OrderTransformEvent($order);
        $this->eventDispatcher->dispatch(TransformerEvents::POST_BASKET_TO_ORDER_TRANSFORM, $event);

        return $order;
    }
}
