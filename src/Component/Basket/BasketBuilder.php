<?php

namespace Sonata\Component\Basket;

use Sonata\Component\Customer\AddressManagerInterface;
use Sonata\Component\Delivery\Pool as DeliveryPool;
use Sonata\Component\Payment\Pool as PaymentPool;
use Sonata\Component\Product\Pool;

class BasketBuilder implements BasketBuilderInterface
{
    /**
     * @var \Sonata\Component\Product\Pool
     */
    protected $productPool;

    /**
     * @var \Sonata\Component\Customer\AddressManagerInterface
     */
    protected $addressManager;

    /**
     * @var \Sonata\Component\Delivery\Pool
     */
    protected $deliveryPool;

    /**
     * @var \Sonata\Component\Payment\Pool
     */
    protected $paymentPool;

    /**
     * @param \Sonata\Component\Product\Pool                     $productPool
     * @param \Sonata\Component\Customer\AddressManagerInterface $addressManager
     * @param \Sonata\Component\Delivery\Pool                    $deliveryPool
     * @param \Sonata\Component\Payment\Pool                     $paymentPool
     */
    public function __construct(Pool $productPool, AddressManagerInterface $addressManager, DeliveryPool $deliveryPool, PaymentPool $paymentPool)
    {
        $this->productPool      = $productPool;
        $this->addressManager   = $addressManager;
        $this->deliveryPool     = $deliveryPool;
        $this->paymentPool      = $paymentPool;
    }

    /**
     * Build a basket.
     *
     * @param \Sonata\Component\Basket\BasketInterface $basket
     *
     * @throws \RuntimeException
     */
    public function build(BasketInterface $basket)
    {
        $basket->setProductPool($this->productPool);

        foreach ($basket->getBasketElements() as $basketElement) {
            if ($basketElement->getProduct() === null) {
                // restore information
                if ($basketElement->getProductCode() == null) {
                    throw new \RuntimeException('The product code is empty');
                }

                $productDefinition = $this->productPool->getProduct($basketElement->getProductCode());
                $basketElement->setProductDefinition($productDefinition);
            }
        }

        // load the delivery address
        $deliveryAddressId = $basket->getDeliveryAddressId();

        if ($deliveryAddressId) {
            $address = $this->addressManager->findOneBy(array('id' => $deliveryAddressId));

            $basket->setDeliveryAddress($address);
        }

        $deliveryMethodCode = $basket->getDeliveryMethodCode();

        if ($deliveryMethodCode) {
            $basket->setDeliveryMethod($this->deliveryPool->getMethod($deliveryMethodCode));
        }

        // load the payment address
        $billingAddressId = $basket->getBillingAddressId();

        if ($billingAddressId) {
            $address = $this->addressManager->findOneBy(array('id' => $billingAddressId));
            $basket->setBillingAddress($address);
        }

        // load the payment method
        $paymentMethodCode = $basket->getPaymentMethodCode();

        if ($paymentMethodCode) {
            $basket->setPaymentMethod($this->paymentPool->getMethod($paymentMethodCode));
        }

        $basket->buildPrices();
    }
}
