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

    protected $product_pool;

    protected $basket_class;

    protected $basket;

    protected $entity_manager;

    protected $delivery_pool;

    protected $payment_pool;

    public function __construct($class)
    {
        $this->basket_class = $class;
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

        if(!$this->basket) {
            $basket = $this->getSession()->get('sonata/basket');

            if(!$basket) {

                if(!class_exists($this->basket_class)) {
                    throw new \RuntimeException(sprintf('unable to load the class %s', $this->basket_class));
                }

                $basket = new $this->basket_class;
            }

            $basket->setProductPool($this->getProductPool());

            try {
                foreach($basket->getElements() as $element) {
                    if($element->getProduct() === null) { // restore information

                        if($element->getProductCode() == null) {
                            throw new \RuntimeException('the product code is empty');
                        }

                        $repository = $this->getProductPool()->getRepository($element->getProductCode());
                        $element->setProductRepository($repository);
                    }
                }


                $delivery_address_id = $basket->getDeliveryAddressId();

                if($delivery_address_id) {
                    $address = $this->getEntityManager()->find('BasketBundle:Address', $delivery_address_id);

                    $basket->setDeliveryAddress($address);
                }


                $billing_address_id = $basket->getPaymentAddressId();
                if($billing_address_id) {
                    $address = $this->getEntityManager()->find('BasketBundle:Address', $billing_address_id);

                    $basket->setPaymentAddress($address);
                }

                $payment_method_code = $basket->getPaymentMethodCode();
                if($payment_method_code) {
                    $basket->setPaymentMethod($this->getPaymentPool()->getMethod($payment_method_code));
                }

            } catch(\Exception $e) {

                // something went wrong while loading the basket
                $basket->reset();
            }


            $this->getSession()->set('sonata/basket', $basket);
            
            $this->basket = $basket;
        }

        return $this->basket;
    }

    public function setProductPool($product_pool)
    {
        $this->product_pool = $product_pool;
    }

    public function getProductPool()
    {
        return $this->product_pool;
    }

    public function setBasketClass($basket_class)
    {
        $this->basket_class = $basket_class;
    }

    public function getBasketClass()
    {
        return $this->basket_class;
    }

    public function setEntityManager($entity_manager)
    {
        $this->entity_manager = $entity_manager;
    }

    public function getEntityManager()
    {
        return $this->entity_manager;
    }

    public function setDeliveryPool($delivery_pool)
    {
        $this->delivery_pool = $delivery_pool;
    }

    public function getDeliveryPool()
    {
        return $this->delivery_pool;
    }

    public function setPaymentPool($payment_pool)
    {
        $this->payment_pool = $payment_pool;
    }

    public function getPaymentPool()
    {
        return $this->payment_pool;
    }

}