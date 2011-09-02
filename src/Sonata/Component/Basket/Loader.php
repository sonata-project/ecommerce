<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Basket;

use Sonata\Component\Product\Pool;
use Symfony\Component\HttpFoundation\Session;
use Sonata\Component\Customer\AddressManagerInterface;
use Sonata\Component\Customer\CustomerManagerInterface;
use Sonata\Component\Delivery\Pool as DeliveryPool;
use Sonata\Component\Payment\Pool as PaymentPool;

class Loader
{
    protected $session;

    protected $productPool;

    protected $basketClass;

    protected $basket;

    protected $addressManager;

    protected $productManager;

    protected $customerManager;

    protected $deliveryPool;

    protected $paymentPool;

    public function __construct($class, Session $session, Pool $productPool, AddressManagerInterface $addressManager,
        DeliveryPool $deliveryPool, PaymentPool $paymentPool, CustomerManagerInterface $customerManager)
    {
        $this->basketClass      = $class;
        $this->addressManager   = $addressManager;
        $this->deliveryPool     = $deliveryPool;
        $this->paymentPool      = $paymentPool;
        $this->session          = $session;
        $this->productPool      = $productPool;
        $this->customerManager  = $customerManager;
    }

    /**
     * @throws \RuntimeException
     * @return \Sonata\Component\Basket\BasketInterface
     */
    private function getBasketInstance()
    {
        $basket = $this->getSession()->get('sonata/basket');

        if (!$basket) {

            if (!class_exists($this->basketClass)) {
                throw new \RuntimeException(sprintf('unable to load the class %s', $this->basketClass));
            }

            $basket = new $this->basketClass;
        }

        return $basket;
    }

    /**
     * @throws \Exception|\RuntimeException
     * @return \Sonata\Component\Basket\BasketInterface
     */
    public function getBasket()
    {
        if (!$this->basket) {

            $basket = $this->getBasketInstance();
            $basket->setProductPool($this->getProductPool());

            try {
                foreach ($basket->getBasketElements() as $basketElement) {
                    if ($basketElement->getProduct() === null) { // restore information

                        if ($basketElement->getProductCode() == null) {
                            throw new \RuntimeException('the product code is empty');
                        }

                        $productDefinition = $this->getProductPool()->getProduct($basketElement->getProductCode());
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
                    $basket->setDeliveryMethod($this->getDeliveryPool()->getMethod($deliveryMethodCode));
                }

                // load the payment address
                $paymentAddressId = $basket->getPaymentAddressId();

                if ($paymentAddressId) {
                    $address = $this->addressManager->findOneBy(array('id' => $paymentAddressId));
                    $basket->setPaymentAddress($address);
                }

                // load the payment method
                $paymentMethodCode = $basket->getPaymentMethodCode();
                if ($paymentMethodCode) {
                    $basket->setPaymentMethod($this->getPaymentPool()->getMethod($paymentMethodCode));
                }

                // customer
                $customerId = $basket->getCustomerId();
                if ($customerId) {
                    $customer = $this->customerManager->findOneBy(array('id' => $customerId));

                    $basket->setCustomer($customer);
                }
            } catch(\Exception $e) {

                throw $e;
                // something went wrong while loading the basket
//                $basket->reset();
            }


            $this->basket = $basket;

            $this->getSession()->set('sonata/basket', $this->basket);
        }

        return $this->basket;
    }

    public function getProductPool()
    {
        return $this->productPool;
    }

    public function getBasketClass()
    {
        return $this->basketClass;
    }

    public function getDeliveryPool()
    {
        return $this->deliveryPool;
    }

    public function getPaymentPool()
    {
        return $this->paymentPool;
    }

    public function getAddressManager()
    {
        return $this->addressManager;
    }

    public function getProductManager()
    {
        return $this->productManager;
    }

    public function getSession()
    {
        return $this->session;
    }
}