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

use Sonata\Component\Payment\PaymentInterface as Payment;
use Sonata\Component\Delivery\DeliveryInterface as Delivery;
use Sonata\Component\Basket\AddressInterface as Address;
use Sonata\Component\Product\ProductInterface as Product;

use Symfony\Component\Validator\Mapping\ClassMetadata;

class Basket implements \Serializable 
{

    protected $elements = array();

    protected $pos = array();

    protected $cptElement = 0;

    protected $inBuild = false;

    protected $product_pool;

    protected $payment_address;

    protected $payment_method;

    protected $payment_method_code;

    protected $payment_address_id;

    protected $delivery_address;

    protected $delivery_method;

    protected $delivery_address_id;

    protected $delivery_method_code;

    public function setProductPool($pool)
    {
        $this->product_pool = $pool;
    }

    public function getProductPool()
    {
        return $this->product_pool;
    }

    /**
     * test is the basket has elements
     *
     * @return boolean
     */
    public function isEmpty()
    {

        return count($this->getElements()) == 0;
    }

    /**
     * Check is the basket is valid : elements, Payment and Delivery information
     *
     * if $element_only is set to true, only elements are checked
     *
     * @param boolean $elements_only
     * @return boolean
     */
    public function isValid($elements_only = false)
    {
        if ($this->isEmpty()) {

            return false;
        }

        foreach ($this->elements as $element)
        {
            if ($element->isValid() === false) {

                return false;
            }
        }

        if ($elements_only) {

            return true;
        }

        if (!$this->getPaymentAddress() instanceof Address) {

            return false;
        }

        if (!$this->getPaymentMethod() instanceof Payment) {

            return false;
        }

        if (!$this->getDeliveryMethod() instanceof Delivery) {

            return false;
        }

        if (!$this->getDeliveryAddress() instanceof Address) {
            if ($this->getDeliveryMethod()->isAddressRequired()) {

                return false;
            }
        }

        return true;
    }

    /**
     * set the Delivery method
     *
     * @param Delivery $method
     */
    public function setDeliveryMethod(Delivery $method = null)
    {
        $this->delivery_method = $method;
    }

    /**
     *
     *
     * @return Delivery
     */
    public function getDeliveryMethod()
    {

        return $this->delivery_method;
    }

    /**
     * set the Delivery address
     *
     * @param Address $address
     */
    public function setDeliveryAddress(Address $address = null)
    {
        $this->delivery_address = $address;
        $this->delivery_address_id = $address ? $address->getId() : null;
    }

    /**
     *
     *
     * @return Address
     */
    public function getDeliveryAddress()
    {

        return $this->delivery_address;
    }

    /**
     * set Payment method
     *
     * @param Payment $method
     */
    public function setPaymentMethod(Payment $method = null)
    {
        $this->payment_method = $method;
        $this->payment_method_code = $method ? $method->getCode() : null;
    }

    /**
     *
     *
     * @return Payment
     */
    public function getPaymentMethod()
    {

        return $this->payment_method;
    }

    /**
     * set the Payment address
     *
     * @param Address $address
     */
    public function setPaymentAddress(Address $address = null)
    {
        $this->payment_address = $address;
        $this->payment_address_id = $address ? $address->getId() : null;
    }

    /**
     *
     *
     * @return Address
     */
    public function getPaymentAddress()
    {

        return $this->payment_address;
    }

    /**
     * Check if the product can be added to the basket
     *
     * @param Product $product
     *
     * @return boolean
     */
    public function isAddable($product)
    {
        $args = array_merge(array($this), func_get_args());

        /*
        * We ask the product repository if it can be added to the basket
        */
        $isAddableBehavior = call_user_func_array(array($this->getProductPool()->getRepository($product), 'isAddableToBasket'), $args);

        return $isAddableBehavior;
    }

    /**
     * reset basket
     *
     */
    public function reset($full = true)
    {

        $this->delivery_address = null;
        $this->delivery_method = null;
        $this->delivery_method_code = null;

        $this->payment_address = null;
        $this->payment_method = null;
        $this->payment_method_code = null;

        if($full) {
            $this->elements = array();
            $this->pos = array();
            $this->cptElement = 0;
        }
    }

    /**
     * return BasketElements
     *
     * @return array BasketElement
     */
    public function getElements()
    {

        return $this->elements;
    }

    /**
     * Warning : this method should be only used by the validation framework
     * 
     * @param  $elements
     * @return void
     */
    public function setElements($elements)
    {
        $this->elements = $elements;
    }
    /**
     * count number of element in the basket
     *
     * @return integer
     */
    public function countElements()
    {

        return count($this->elements);
    }

    /**
     * return true if the basket has some elements ...
     *
     * @return boolean
     */
    public function hasElements()
    {

        return $this->countElements() > 0;
    }

    /**
     * return the BasketElement depends on the $product or the position from the element stacks
     *
     * @param mixed $product
     *
     * @return Product
     */
    public function getElement($product) {
        if (is_object($product)) {
            $pos = $this->pos[$product->getId()];
        }
        else
        {
            $pos = $this->pos[$product];
        }

        return isset($this->elements[$pos]) ? $this->elements[$pos] : null;
    }

    /**
     * delete an element from the basket depend on the $element. Element
     * can be a product or a basket element
     *
     * @param mixed $element
     *
     * @return BasketElement
     */
    public function removeElement($element)
    {

        if ($element instanceof Product) {
            $pos = $this->pos[$element->getId()];
            $element = $this->elements[$pos];
        }
        else
        {
            $pos = $element->getPos();
        }

        unset($this->elements[$pos]);

        if (!$this->inBuild) {
            $this->buildPrices();
        }

        return $element;
    }

    /**
     * Add an element into the basket, the product behavior manage this action
     *
     * @param Product $product
     *
     * @return BasketElement
     */
    public function addProduct(Product $product)
    {

        $this->reset(false);

        $args = func_get_args();
        array_shift($args);

        $args = array_merge(array($this, $product), count($args) == 0 ? array(array()) : $args);
        
        return call_user_func_array(array($this->getProductPool()->getRepository($product), 'basketAddProduct'), $args);
    }

    /**
     * Merge one Product with another Product, the product
     * must have the same id.
     *
     * The product behavior manages this action
     *
     * @param Product $product
     * @return BasketElement
     */
    public function mergeProduct(Product $product)
    {

        $args = func_get_args();
        array_shift($args);
        $args = array_merge(array($this, $product), count($args) == 0 ? array(array()) : $args);

        return call_user_func_array(array($this->getProductPool()->getRepository($product), 'basketMergeProduct'), $args);
    }

    /**
     * Add a basket element into the current basket
     *
     * @param BasketElement $basket_element
     */
    public function addBasketElement($basket_element)
    {

        $this->reset(false);
        
        $basket_element->setPos($this->cptElement);

        $this->elements[$this->cptElement] = $basket_element;
        $this->pos[$basket_element->getProduct()->getId()] = $this->cptElement;
        
        $this->cptElement++;

        $this->buildPrices();
    }

    /**
     * return true if the basket has a least one recurrent product (subscription)
     *
     *  @return boolean
     */
    public function hasRecurrentPayment()
    {
        foreach ($this->elements as $element) {
            $product = $element->getProduct();
            if ($product instanceof Product) {
                if ($product->isRecurrentPayment() === true) {

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * return the total of the basket
     * if $vat = true, return price with vat
     * if $recurrent_only = true, return price for recurent product only
     * if $recurrent_only = false, return price for non recurent product only
     *
     * @param boolean $tva
     * @param boolean $recurrent_only
     *
     * @return float
     */
    public function getTotal($vat = false, $recurrent_only = null)
    {
        $total = 0;
        foreach ($this->elements as $element) {

            $product = $element->getProduct();

            if ($recurrent_only === true && $product->isRecurrentPayment() === false) {
                continue;
            }

            if ($recurrent_only === false && $product->isRecurrentPayment() === true) {
                continue;
            }

            $total += $element->getTotal($vat);
        }

        $total += $this->getDeliveryPrice($vat);

        return bcadd($total, 0, 2);
    }

    /**
     * return the VAT of the current basket
     *
     * @return float
     */
    public function getVatAmount()
    {
        $vat = 0;
        foreach ($this->elements as $element) {
            $vat += $element->getVatAmount();
        }

        $delivery_method = $this->getDeliveryMethod();

        if ($delivery_method instanceof Delivery) {
            $vat += $delivery_method->getVatAmount($this);
        }

        return $vat;
    }

    /**
     * return the Delivery price
     *
     *
     * @param Boolean $tva
     * @return float
     */
    public function getDeliveryPrice($vat = false)
    {

        $method = $this->getDeliveryMethod();
        if (!$method instanceof Delivery) {
            return 0;
        }

        return $method->getTotal($this, $vat);
    }

    /**
     * check if the basket contains $product
     *
     * @param Product $product
     * @return boolean
     */
    public function hasProduct(Product $product)
    {
        if (!array_key_exists($product->getId(), $this->pos)) {
            return false;
        }

        $pos = $this->pos[$product->getId()];

        if (!array_key_exists($pos, $this->elements)) {
            return false;
        }

        if ($this->elements[$pos] instanceof BasketElement) {
            return true;
        }

        return false;
    }

    /**
     * Compute the price of the basket
     *
     */
    public function buildPrices()
    {
        $this->inBuild = true;

        foreach ($this->elements as $element) {
            $product = $element->getProduct();

            if (!is_object($product)) {
                $this->removeElement($element);
                
                continue;
            }

            $repository = $this->getProductPool()->getRepository($product);
            if($repository) {
                $price = $repository->basketCalculatePrice($this, $element);
                $element->setPrice($price);
            }
            
        }

        $this->inBuild = false;
    }

    /**
     * remove basket element market as deleted
     * @return void
     */
    public function clean()
    {

        foreach($this->getElements() as $basket_element) {
            if($basket_element->getDelete()) {
                $this->removeElement($basket_element);
            }
        }
    }

    public function serialize()
    {
        
        return serialize(array(
            'elements'              => $this->elements,
            'pos'                   => $this->pos,
            'delivery_address_id'   => $this->delivery_address_id,
            'delivery_method'       => $this->delivery_method,
            'payment_address_id'    => $this->payment_address_id,
            'payment_method_code'   => $this->payment_method_code,
            'cptElement'            => $this->cptElement,
            'delivery_method_code'  => $this->delivery_method_code
        ));
    }
    
    public function unserialize($data)
    {

        $data = unserialize($data);

        $properties = array(
            'elements',
            'pos',
            'delivery_address_id',
            'delivery_method',
            'delivery_method_code',
            'payment_address_id',
            'payment_method_code',
            'cptElement',
        );

        foreach($properties as $property)
        {
            $this->$property = isset($data[$property]) ? $data[$property] : $this->$property;
        }
    }

    public function setDeliveryAddressId($delivery_address_id)
    {
        $this->delivery_address_id = $delivery_address_id;
    }

    public function getDeliveryAddressId()
    {
        return $this->delivery_address_id;
    }

    public function setPaymentAddressId($payment_address_id)
    {
        $this->payment_address_id = $payment_address_id;
    }

    public function getPaymentAddressId()
    {
        return $this->payment_address_id;
    }

    public function setPaymentMethodCode($payment_method_code)
    {
        $this->payment_method_code = $payment_method_code;
    }

    public function getPaymentMethodCode()
    {
        return $this->payment_method_code;
    }

}