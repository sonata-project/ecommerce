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
use Symfony\Component\Security\Core\SecurityContextInterface;

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

    protected $securityContext;

    public function __construct($class, Session $session, Pool $productPool, AddressManagerInterface $addressManager,
        DeliveryPool $deliveryPool, PaymentPool $paymentPool, CustomerManagerInterface $customerManager,
        SecurityContextInterface $securityContext)
    {
        $this->basketClass      = $class;
        $this->addressManager   = $addressManager;
        $this->deliveryPool     = $deliveryPool;
        $this->paymentPool      = $paymentPool;
        $this->session          = $session;
        $this->productPool      = $productPool;
        $this->customerManager  = $customerManager;
        $this->securityContext  = $securityContext;
    }

    /**
     * @throws \RuntimeException
     * @return \Sonata\Component\Basket\BasketInterface
     */
    private function getBasketInstance()
    {
        $basket = $this->session->get('sonata/basket');

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

            try {
                $this->basket = $this->buildBasket();
            } catch(\Exception $e) {

                throw $e;
                // something went wrong while loading the basket
//                $basket->reset();
            }

            $this->session->set('sonata/basket', $this->basket);
        }

        return $this->basket;
    }

    /**
     * @throws \RuntimeException
     * @return \Sonata\Component\Basket\BasketInterface
     */
    protected function buildBasket()
    {
        $basket = $this->getBasketInstance();
        $basket->setProductPool($this->productPool);

        foreach ($basket->getBasketElements() as $basketElement) {
            if ($basketElement->getProduct() === null) { // restore information

                if ($basketElement->getProductCode() == null) {
                    throw new \RuntimeException('the product code is empty');
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
        $paymentAddressId = $basket->getPaymentAddressId();

        if ($paymentAddressId) {
            $address = $this->addressManager->findOneBy(array('id' => $paymentAddressId));
            $basket->setPaymentAddress($address);
        }

        // load the payment method
        $paymentMethodCode = $basket->getPaymentMethodCode();
        if ($paymentMethodCode) {
            $basket->setPaymentMethod($this->paymentPool->getMethod($paymentMethodCode));
        }

        // customer
        $customerId = $basket->getCustomerId();
        $user = $this->securityContext->getToken()->getUser();

        if ($customerId) {
            $customer = $this->customerManager->findOneBy(array('id' => $customerId));

            if ($customer && $customer->getUser()->getId() != $user->getId()) {
                throw new \RuntimeException('Invalid basket state');
            }

            $basket->setCustomer($customer);
        }

        if (!$basket->getCustomer()) {
            $basket->setCustomer($this->customerManager->getMainCustomer($user));
        }

        return $basket;
    }
}