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

class Loader
{
    protected $session;

    protected $productPool;

    protected $basketClass;

    protected $basket;

    protected $entityManager;

    protected $deliveryPool;

    protected $paymentPool;

    public function __construct($class)
    {
        $this->basketClass = $class;
    }

    public function setSession($session)
    {
        $this->session = $session;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function getBasket()
    {

        if (!$this->basket) {
            $basket = $this->getSession()->get('sonata/basket');

            if (!$basket) {

                if (!class_exists($this->basketClass)) {
                    throw new \RuntimeException(sprintf('unable to load the class %s', $this->basketClass));
                }

                $basket = new $this->basketClass;
            }

            $basket->setProductPool($this->getProductPool());

            try {
                foreach ($basket->getBasketElements() as $basketElement) {
                    if ($basketElement->getProduct() === null) { // restore information

                        if ($basketElement->getProductCode() == null) {
                            throw new \RuntimeException('the product code is empty');
                        }

                        $repository = $this->getProductPool()->getRepository($basketElement->getProductCode());
                        $basketElement->setProductRepository($repository);
                    }
                }


                // load the delivery address
                $deliveryAddressId = $basket->getDeliveryAddressId();

                if ($deliveryAddressId) {
                    $address = $this->getEntityManager()->find('Application\Sonata\CustomerBundle\Entity\Address', $deliveryAddressId);

                    $basket->setDeliveryAddress($address);
                }

                // load the payment address
                $paymentAddressId = $basket->getPaymentAddressId();
                if ($paymentAddressId) {
                    $address = $this->getEntityManager()->find('Application\Sonata\CustomerBundle\Entity\Address', $paymentAddressId);

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
                    $customer = $this->getEntityManager()->find('Application\Sonata\CustomerBundle\Entity\Customer', $customerId);

                    $basket->setCustomer($customer);
                }

            } catch(\Exception $e) {

                throw $e;
                // something went wrong while loading the basket
                $basket->reset();
            }


            $this->getSession()->set('sonata/basket', $basket);
            
            $this->basket = $basket;
        }

        return $this->basket;
    }

    public function setProductPool($productPool)
    {
        $this->productPool = $productPool;
    }

    public function getProductPool()
    {
        return $this->productPool;
    }

    public function setBasketClass($basketClass)
    {
        $this->basketClass = $basketClass;
    }

    public function getBasketClass()
    {
        return $this->basketClass;
    }

    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getEntityManager()
    {
        return $this->entityManager;
    }

    public function setDeliveryPool($deliveryPool)
    {
        $this->deliveryPool = $deliveryPool;
    }

    public function getDeliveryPool()
    {
        return $this->deliveryPool;
    }

    public function setPaymentPool($paymentPool)
    {
        $this->paymentPool = $paymentPool;
    }

    public function getPaymentPool()
    {
        return $this->paymentPool;
    }

}