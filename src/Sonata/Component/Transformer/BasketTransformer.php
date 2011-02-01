<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Transformer;


class BasketTransformer extends BaseTransformer
{


    /**
     * transform a basket into order
     *
     * @param  $customer
     * @param  $basket
     * @return Order
     */
    public function transformIntoOrder($customer, $basket)
    {

        // Customer
        if (!is_object($customer)) {

            if ($this->getLogger()) {
                $this->getLogger()->emerg('[Sonata\Component\Payment\Transform\Basket::transform] the customer is not valid');
            }

            throw new \RuntimeException('Invalid customer');
        }

        // Basket
        if (!$basket) {

            if ($this->getLogger()) {
                $this->getLogger()->emerg('[Sonata\Component\Payment\Transform\Basket::transform] the basket is not defined');
            }

            throw new \RuntimeException('Invalid basket');
        }
        
        // Billing
        $billingAddress = $basket->getPaymentAddress();
        if (!$billingAddress instanceof  \Sonata\Component\Basket\AddressInterface) {

            if ($this->getLogger()) {
                $this->getLogger()->emerg('[Sonata\Component\Payment\Transform\Basket::transform] the billing address is not valid');
            }

            throw new \RuntimeException('Invalid billing address');
        }

        // Shipping
        $deliveryMethod = $basket->getDeliveryMethod();
        if (!$deliveryMethod instanceof \Sonata\Component\Delivery\DeliveryInterface) {

            if ($this->getLogger()) {
                $this->getLogger()->emerg('[Sonata\Component\Delivery\DeliveryInterface::transform] the delivery method is not valid');
            }

            throw new \RuntimeException('Invalid delivery method');
        }

        $deliveryAddress = $basket->getDeliveryAddress();
        if ($deliveryMethod->isAddressRequired() && !$deliveryAddress instanceof \Sonata\Component\Basket\AddressInterface) {

            if ($this->getLogger()) {
                $this->getLogger()->emerg('[Sonata\Component\Delivery\DeliveryInterface::transform] the shipping address is not valid');
            }

            throw new \RuntimeException('Invalid delivery method');
        }

        // add a custom class_instance for testing purpose.
        // todo : find a cleaner way to do that
        $order = $this->getOption('order_instance') ? $this->getOption('order_instance') : new \Application\Sonata\OrderBundle\Entity\Order;

        $order->setCustomer($customer);

        $order->setUsername($customer->getFullname());

        if ($deliveryMethod->isAddressRequired()) {
            $order->setShippingAddress1($deliveryAddress->getAddress1());
            $order->setShippingAddress2($deliveryAddress->getAddress2());
            $order->setShippingAddress3($deliveryAddress->getAddress3());
            $order->setShippingPostcode($deliveryAddress->getPostcode());
            $order->setShippingCity($deliveryAddress->getCity());
            $order->setShippingCountryCode($deliveryAddress->getCountryCode());
            $order->setShippingName($deliveryAddress->getName());
            $order->setShippingPhone($deliveryAddress->getPhone());
        }

        $order->setBillingAddress1($billingAddress->getAddress1());
        $order->setBillingAddress2($billingAddress->getAddress2());
        $order->setBillingAddress3($billingAddress->getAddress3());
        $order->setBillingPostcode($billingAddress->getPostcode());
        $order->setBillingCity($billingAddress->getCity());
        $order->setBillingCountryCode($billingAddress->getCountryCode());
        $order->setBillingName($billingAddress->getName());
        $order->setBillingPhone($billingAddress->getPhone());

        $order->setTotalExcl($basket->getTotal());
        $order->setTotalInc($basket->getTotal(true));

        $order->setDeliveryCost($basket->getDeliveryPrice(true));
        $order->setDeliveryMethod($basket->getDeliveryMethod()->getCode());
        $order->setDeliveryStatus(\Sonata\Component\Delivery\DeliveryInterface::STATUS_OPEN);

        $order->setCreatedAt(new \DateTime);
        
        // todo : handle the currency
        //$order->setCurrency(Product::getDefaultCurrency());

        $order->setStatus(\Sonata\Component\Order\OrderInterface::STATUS_OPEN);

        $order->setPaymentStatus(\Application\Sonata\PaymentBundle\Entity\Transaction::STATUS_OPEN);
        $order->setPaymentMethod($basket->getPaymentMethod()->getCode());

        foreach ($basket->getBasketElements() as $basketElement)
        {
            $orderElement = $basketElement->getProductRepository()->createOrderElement($basketElement);

            $order->addOrderElement($orderElement);
        }


        return $order;
    }
}