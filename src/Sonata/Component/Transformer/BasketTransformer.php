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


class BasketTransformer extends BaseTransformer {


    /**
     * transform a basket into order
     *
     * @param  $user
     * @param  $basket
     * @return Order
     */
    public function transformIntoOrder($user, $basket) {

        // User
        if (!is_object($user)) {

            if($this->getLogger()) {
                $this->getLogger()->emerg('[Sonata\Component\Payment\Transform\Basket::transform] the user is not valid');
            }

            throw new \RuntimeException('Invalid user');
        }

        if (!$basket) {

            if($this->getLogger()) {
                $this->getLogger()->emerg('[Sonata\Component\Payment\Transform\Basket::transform] the basket is not defined');
            }

            throw new \RuntimeException('Invalid basket');
        }

        
        // Billing
        $billing_address = $basket->getPaymentAddress();
        if (!$billing_address instanceof  \Sonata\Component\Basket\AddressInterface) {

            if($this->getLogger()) {
                $this->getLogger()->emerg('[Sonata\Component\Payment\Transform\Basket::transform] the billing address is not valid');
            }

            throw new \RuntimeException('Invalid billing address');
        }

        // Shipping
        $delivery_method = $basket->getDeliveryMethod();
        if (!$delivery_method instanceof \Sonata\Component\Delivery\DeliveryInterface) {

            if($this->getLogger()) {
                $this->getLogger()->emerg('[Sonata\Component\Delivery\DeliveryInterface::transform] the delivery method is not valid');
            }

            throw new \RuntimeException('Invalid delivery method');
        }

        $delivery_address = $basket->getDeliveryAddress();
        if ($delivery_method->isAddressRequired() && !$delivery_address instanceof \Sonata\Component\Basket\AddressInterface) {

            if($this->getLogger()) {
                $this->getLogger()->emerg('[Sonata\Component\Delivery\DeliveryInterface::transform] the shipping address is not valid');
            }

            throw new \RuntimeException('Invalid delivery method');
        }

        // add a custom class_instance for testing purpose.
        // todo : find a cleaner way to do that
        $order = $this->getOption('order_instance') ? $this->getOption('order_instance') : new \Application\Sonata\OrderBundle\Entity\Order;

        $order->setUser($user);

        $order->setUsername($user->getUsername());

        if ($delivery_method->isAddressRequired()) {
            $order->setShippingAddress1($delivery_address->getAddress1());
            $order->setShippingAddress2($delivery_address->getAddress2());
            $order->setShippingAddress3($delivery_address->getAddress3());
            $order->setShippingPostcode($delivery_address->getPostcode());
            $order->setShippingCity($delivery_address->getCity());
            $order->setShippingCountryCode($delivery_address->getCountryCode());
            $order->setShippingName($delivery_address->getName());
            $order->setShippingPhone($delivery_address->getPhone());
        }

        $order->setBillingAddress1($billing_address->getAddress1());
        $order->setBillingAddress2($billing_address->getAddress2());
        $order->setBillingAddress3($billing_address->getAddress3());
        $order->setBillingPostcode($billing_address->getPostcode());
        $order->setBillingCity($billing_address->getCity());
        $order->setBillingCountryCode($billing_address->getCountryCode());
        $order->setBillingName($billing_address->getName());
        $order->setBillingPhone($billing_address->getPhone());

        $order->setTotalExcl($basket->getTotal());
        $order->setTotalInc($basket->getTotal(true));

        $order->setDeliveryCost($basket->getDeliveryPrice(true));
        $order->setDeliveryMethod($basket->getDeliveryMethod()->getCode());
        $order->setDeliveryStatus(\Sonata\Component\Delivery\DeliveryInterface::STATUS_OPEN);

        $order->setCreatedAt(new \DateTime);
        
        // todo : handle the currency
        //$order->setCurrency(Product::getDefaultCurrency());

        $order->setStatus(\Sonata\Component\Order\OrderInterface::STATUS_OPEN);

        $order->setPaymentStatus(\Application\PaymentBundle\Entity\Transaction::STATUS_OPEN);
        $order->setPaymentMethod($basket->getPaymentMethod()->getCode());

        foreach ($basket->getElements() as $basket_element)
        {
            $order_element = $basket_element->getProductRepository()->createOrderElement($basket_element);

            $order->addOrderElement($order_element);
        }


        return $order;
    }
}