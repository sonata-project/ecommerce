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

namespace Sonata\Component\Basket;

use Sonata\Component\Customer\AddressManagerInterface;
use Sonata\Component\Delivery\Pool as DeliveryPool;
use Sonata\Component\Payment\Pool as PaymentPool;
use Sonata\Component\Product\Pool;

class BasketBuilder implements BasketBuilderInterface
{
    /**
     * @var Pool
     */
    protected $productPool;

    /**
     * @var AddressManagerInterface
     */
    protected $addressManager;

    /**
     * @var DeliveryPool
     */
    protected $deliveryPool;

    /**
     * @var PaymentPool
     */
    protected $paymentPool;

    /**
     * @param Pool                    $productPool
     * @param AddressManagerInterface $addressManager
     * @param DeliveryPool            $deliveryPool
     * @param PaymentPool             $paymentPool
     */
    public function __construct(
        Pool $productPool,
        AddressManagerInterface $addressManager,
        DeliveryPool $deliveryPool,
        PaymentPool $paymentPool
    ) {
        $this->productPool = $productPool;
        $this->addressManager = $addressManager;
        $this->deliveryPool = $deliveryPool;
        $this->paymentPool = $paymentPool;
    }

    /**
     * Build a basket.
     *
     * @param BasketInterface $basket
     *
     * @throws \RuntimeException
     */
    public function build(BasketInterface $basket): void
    {
        $basket->setProductPool($this->productPool);

        foreach ($basket->getBasketElements() as $basketElement) {
            if (null === $basketElement->getProduct()) {
                // restore information
                if (null == $basketElement->getProductCode()) {
                    throw new \RuntimeException('The product code is empty');
                }

                $productDefinition = $this->productPool->getProduct($basketElement->getProductCode());
                $basketElement->setProductDefinition($productDefinition);
            }
        }

        // load the delivery address
        $deliveryAddressId = $basket->getDeliveryAddressId();

        if ($deliveryAddressId) {
            $address = $this->addressManager->findOneBy(['id' => $deliveryAddressId]);

            $basket->setDeliveryAddress($address);
        }

        $deliveryMethodCode = $basket->getDeliveryMethodCode();

        if ($deliveryMethodCode) {
            $basket->setDeliveryMethod($this->deliveryPool->getMethod($deliveryMethodCode));
        }

        // load the payment address
        $billingAddressId = $basket->getBillingAddressId();

        if ($billingAddressId) {
            $address = $this->addressManager->findOneBy(['id' => $billingAddressId]);
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
