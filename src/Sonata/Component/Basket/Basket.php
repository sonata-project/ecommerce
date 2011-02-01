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

    protected $basketElements = array();

    protected $pos = array();

    protected $cptElement = 0;

    protected $inBuild = false;

    protected $productPool;

    protected $paymentAddress;

    protected $paymentMethod;

    protected $paymentMethodCode;

    protected $paymentAddressId;

    protected $deliveryAddress;

    protected $deliveryMethod;

    protected $deliveryAddressId;

    protected $deliveryMethodCode;

    protected $customer;

    protected $customerId;

    public function setProductPool($pool)
    {
        $this->productPool = $pool;
    }

    public function getProductPool()
    {
        return $this->productPool;
    }

    /**
     * test is the basket has elements
     *
     * @return boolean
     */
    public function isEmpty()
    {

        return count($this->getBasketElements()) == 0;
    }

    /**
     * Check is the basket is valid : elements, Payment and Delivery information
     *
     * if $element_only is set to true, only elements are checked
     *
     * @param boolean $elements_only
     * @return boolean
     */
    public function isValid($elementsOnly = false)
    {
        if ($this->isEmpty()) {

            return false;
        }

        foreach ($this->getBasketElements() as $element)
        {
            if ($element->isValid() === false) {

                return false;
            }
        }

        if ($elementsOnly) {

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
        $this->deliveryMethod = $method;
    }

    /**
     *
     *
     * @return Delivery
     */
    public function getDeliveryMethod()
    {

        return $this->deliveryMethod;
    }

    /**
     * set the Delivery address
     *
     * @param Address $address
     */
    public function setDeliveryAddress(Address $address = null)
    {
        $this->deliveryAddress = $address;
        $this->deliveryAddressId = $address ? $address->getId() : null;
    }

    /**
     *
     *
     * @return Address
     */
    public function getDeliveryAddress()
    {

        return $this->deliveryAddress;
    }

    /**
     * set Payment method
     *
     * @param Payment $method
     */
    public function setPaymentMethod(Payment $method = null)
    {
        $this->paymentMethod = $method;
        $this->paymentMethodCode = $method ? $method->getCode() : null;
    }

    /**
     *
     *
     * @return Payment
     */
    public function getPaymentMethod()
    {

        return $this->paymentMethod;
    }

    /**
     * set the Payment address
     *
     * @param Address $address
     */
    public function setPaymentAddress(Address $address = null)
    {
        $this->paymentAddress = $address;
        $this->paymentAddressId = $address ? $address->getId() : null;
    }

    /**
     *
     *
     * @return Address
     */
    public function getPaymentAddress()
    {

        return $this->paymentAddress;
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

        $this->deliveryAddressId = null;
        $this->deliveryAddress = null;
        $this->deliveryMethod = null;
        $this->deliveryMethodCode = null;

        $this->paymentAddressId = null;
        $this->paymentAddress = null;
        $this->paymentMethod = null;
        $this->paymentMethodCode = null;

        if ($full) {
            $this->basketElements = array();
            $this->pos = array();
            $this->cptElement = 0;
            $this->customerId = null;
            $this->customer = null;
        }
    }

    /**
     * return BasketElements
     *
     * @return array BasketElement
     */
    public function getBasketElements()
    {

        return $this->basketElements;
    }

    /**
     * Warning : this method should be only used by the validation framework
     * 
     * @param  $elements
     * @return void
     */
    public function setBasketElements($elements)
    {
        $this->basketElements = $elements;
    }
    /**
     * count number of element in the basket
     *
     * @return integer
     */
    public function countBasketElements()
    {

        return count($this->basketElements);
    }

    /**
     * return true if the basket has some elements ...
     *
     * @return boolean
     */
    public function hasBasketElements()
    {

        return $this->countBasketElements() > 0;
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

        return isset($this->basketElements[$pos]) ? $this->basketElements[$pos] : null;
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
            $element = $this->basketElements[$pos];
        }
        else
        {
            $pos = $element->getPos();
        }

        unset($this->basketElements[$pos]);

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
     * @param BasketElement $basketElement
     */
    public function addBasketElement($basketElement)
    {

        $this->reset(false);
        
        $basketElement->setPos($this->cptElement);

        $this->basketElements[$this->cptElement] = $basketElement;
        $this->pos[$basketElement->getProduct()->getId()] = $this->cptElement;
        
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
        foreach ($this->getBasketElements() as $basketElement) {
            $product = $basketElement->getProduct();
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
    public function getTotal($vat = false, $recurrentOnly = null)
    {
        $total = 0;
        foreach ($this->getBasketElements() as $basketElement) {

            $product = $basketElement->getProduct();

            if ($recurrentOnly === true && $product->isRecurrentPayment() === false) {
                continue;
            }

            if ($recurrentOnly === false && $product->isRecurrentPayment() === true) {
                continue;
            }

            $total += $basketElement->getTotal($vat);
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
        foreach ($this->getBasketElements() as $basketElement) {
            $vat += $basketElement->getVatAmount();
        }

        $deliveryMethod = $this->getDeliveryMethod();

        if ($deliveryMethod instanceof Delivery) {
            $vat += $deliveryMethod->getVatAmount($this);
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

        if (!array_key_exists($pos, $this->getBasketElements())) {
            return false;
        }

        if ($this->basketElements[$pos] instanceof BasketElement) {
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

        foreach ($this->getBasketElements() as $basketElement) {
            $product = $basketElement->getProduct();

            if (!is_object($product)) {
                $this->removeElement($basketElement);
                
                continue;
            }

            $repository = $this->getProductPool()->getRepository($product);
            if ($repository) {
                $price = $repository->basketCalculatePrice($this, $basketElement);
                $basketElement->setPrice($price);
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

        foreach ($this->getBasketElements() as $basketElement) {
            if ($basketElement->getDelete()) {
                $this->removeElement($basketElement);
            }
        }
    }

    public function serialize()
    {
        
        return serialize(array(
            'basketElements'        => $this->getBasketElements(),
            'pos'                   => $this->pos,
            'deliveryAddressId'     => $this->deliveryAddressId,
            'deliveryMethod'        => $this->deliveryMethod,
            'paymentAddressId'      => $this->paymentAddressId,
            'paymentMethodCode'     => $this->paymentMethodCode,
            'cptElement'            => $this->cptElement,
            'deliveryMethodCode'    => $this->deliveryMethodCode,
            'customerId'            => $this->customerId,

        ));
    }
    
    public function unserialize($data)
    {

        $data = unserialize($data);

        $properties = array(
            'basketElements',
            'pos',
            'deliveryAddressId',
            'deliveryMethod',
            'deliveryMethodCode',
            'paymentAddressId',
            'paymentMethodCode',
            'cptElement',
            'customerId',
        );

        foreach ($properties as $property)
        {
            $this->$property = isset($data[$property]) ? $data[$property] : $this->$property;
        }
    }

    public function setDeliveryAddressId($deliveryAddressId)
    {
        $this->deliveryAddressId = $deliveryAddressId;
    }

    public function getDeliveryAddressId()
    {
        return $this->deliveryAddressId;
    }

    public function setPaymentAddressId($paymentAddressId)
    {
        $this->paymentAddressId = $paymentAddressId;
    }

    public function getPaymentAddressId()
    {
        return $this->paymentAddressId;
    }

    public function setPaymentMethodCode($paymentMethodCode)
    {
        $this->paymentMethodCode = $paymentMethodCode;
    }

    public function getPaymentMethodCode()
    {
        return $this->paymentMethodCode;
    }

    public function setCustomer($customer)
    {
        $this->customer = $customer;
        $this->customerId = $customer ? $customer->getId() : null;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    }

    public function getCustomerId()
    {
        return $this->customerId;
    }

}