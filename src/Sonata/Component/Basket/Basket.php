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

use Sonata\Component\Payment\PaymentInterface;
use Sonata\Component\Delivery\DeliveryInterface;
use Sonata\Component\Customer\AddressInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Customer\CustomerInterface;
use Sonata\Component\Product\Pool;

class Basket implements \Serializable, BasketInterface
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

    protected $options = array();

    public function setProductPool(Pool $pool)
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
     * @param boolean $elementsOnly
     * @return boolean
     */
    public function isValid($elementsOnly = false)
    {
        if ($this->isEmpty()) {
            return false;
        }

        foreach ($this->getBasketElements() as $element) {
            if ($element->isValid() === false) {
                return false;
            }
        }

        if ($elementsOnly) {
            return true;
        }

        if (!$this->getPaymentAddress() instanceof AddressInterface) {
            return false;
        }

        if (!$this->getPaymentMethod() instanceof PaymentInterface) {
            return false;
        }

        if (!$this->getDeliveryMethod() instanceof DeliveryInterface) {
            return false;
        }

        if (!$this->getDeliveryAddress() instanceof AddressInterface) {
            if ($this->getDeliveryMethod()->isAddressRequired()) {
                return false;
            }
        }

        return true;
    }

    /**
     * set the Delivery method
     *
     * @param DeliveryInterface $method
     */
    public function setDeliveryMethod(DeliveryInterface $method = null)
    {
        $this->deliveryMethod = $method;
        $this->deliveryMethodCode = $method ? $method->getCode() : null;
    }

    /**
     *
     *
     * @return DeliveryInterface
     */
    public function getDeliveryMethod()
    {
        return $this->deliveryMethod;
    }

    /**
     * set the Delivery address
     *
     * @param AddressInterface $address
     */
    public function setDeliveryAddress(AddressInterface $address = null)
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
     * @param PaymentInterface $method
     */
    public function setPaymentMethod(PaymentInterface $method = null)
    {
        $this->paymentMethod = $method;
        $this->paymentMethodCode = $method ? $method->getCode() : null;
    }

    /**
     *
     *
     * @return PaymentInterface
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * set the Payment address
     *
     * @param AddressInterface $address
     */
    public function setPaymentAddress(AddressInterface $address = null)
    {
        $this->paymentAddress = $address;
        $this->paymentAddressId = $address ? $address->getId() : null;
    }

    /**
     *
     *
     * @return AddressInterface
     */
    public function getPaymentAddress()
    {
        return $this->paymentAddress;
    }

    /**
     * Check if the product can be added to the basket
     *
     * @param ProductInterface $product
     *
     * @return boolean
     */
    public function isAddable(ProductInterface $product)
    {
        /*
        * We ask the product repository if it can be added to the basket
        */
        $isAddableBehavior = call_user_func_array(
            array($this->getProductPool()->getProvider($product), 'isAddableToBasket'),
            array_merge(array($this), func_get_args())
        );

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
            $this->options = array();
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
     * @return ProductInterface
     */
    public function getElement(ProductInterface $product)
    {
        if (is_object($product)) {
            $pos = $this->pos[$product->getId()];
        } else {
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
     * @return BasketElementInterface
     */
    public function removeElement(BasketElementInterface $element)
    {
        if ($element instanceof ProductInterface) {
            $pos = $this->pos[$element->getId()];
            $element = $this->basketElements[$pos];
        } else {
            $pos = $element->getPos();
        }

        unset($this->basketElements[$pos]);

        if (!$this->inBuild) {
            $this->buildPrices();
        }

        return $element;
    }

    /**
     * Add a basket element into the current basket
     *
     * @param BasketElementInterface $basketElement
     */
    public function addBasketElement(BasketElementInterface $basketElement)
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

            if ($product instanceof ProductInterface) {
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
     * @param boolean $vat
     * @param boolean $recurrentOnly
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

        if ($deliveryMethod instanceof DeliveryInterface) {
            $vat += $deliveryMethod->getVatAmount($this);
        }

        return $vat;
    }

    /**
     * return the Delivery price
     *
     *
     * @param Boolean $vat
     * @return float
     */
    public function getDeliveryPrice($vat = false)
    {
        $method = $this->getDeliveryMethod();

        if (!$method instanceof DeliveryInterface) {
            return 0;
        }

        return $method->getTotal($this, $vat);
    }

    /**
     * check if the basket contains $product
     *
     * @param ProductInterface $product
     * @return boolean
     */
    public function hasProduct(ProductInterface $product)
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

            $provider = $this->getProductPool()->getProvider($product);
            $price    = $provider->basketCalculatePrice($this, $basketElement);
            $basketElement->setPrice($price);
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
            'paymentAddressId'      => $this->paymentAddressId,
            'paymentMethodCode'     => $this->paymentMethodCode,
            'cptElement'            => $this->cptElement,
            'deliveryMethodCode'    => $this->deliveryMethodCode,
            'customerId'            => $this->customerId,
            'options'               => $this->options,
        ));
    }

    public function unserialize($data)
    {
        $data = unserialize($data);

        $properties = array(
            'basketElements',
            'pos',
            'deliveryAddressId',
            'deliveryMethodCode',
            'paymentAddressId',
            'paymentMethodCode',
            'cptElement',
            'customerId',
            'options',
        );

        foreach ($properties as $property) {
            $this->$property = isset($data[$property]) ? $data[$property] : $this->$property;
        }
    }

    /**
     * @param int $deliveryAddressId
     * @return void
     */
    public function setDeliveryAddressId($deliveryAddressId)
    {
        $this->deliveryAddressId = $deliveryAddressId;
    }

    /**
     * @return int
     */
    public function getDeliveryAddressId()
    {
        return $this->deliveryAddressId;
    }

    /**
     * @param $paymentAddressId
     * @return void
     */
    public function setPaymentAddressId($paymentAddressId)
    {
        $this->paymentAddressId = $paymentAddressId;
    }

    /**
     * @return mixed
     */
    public function getPaymentAddressId()
    {
        return $this->paymentAddressId;
    }

    /**
     * @return
     */
    public function getPaymentMethodCode()
    {
        return $this->paymentMethodCode;
    }

    /**
     * @return string
     */
    public function getDeliveryMethodCode()
    {
        return $this->deliveryMethodCode;
    }

    /**
     * @param \Sonata\Component\Customer\CustomerInterface $customer
     * @return void
     */
    public function setCustomer(CustomerInterface $customer)
    {
        $this->customer = $customer;
        $this->customerId = $customer ? $customer->getId() : null;
    }

    /**
     * @return
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param $customerId
     * @return void
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * @return
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param $options
     * @return void
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @param $name
     * @param null $default
     * @return null
     */
    public function getOption($name, $default = null)
    {
        if (!array_key_exists($name, $this->options)) {
            return $default;
        }

        return $this->options[$name];
    }

    /**
     * @param $name
     * @param mixed $value
     * @return void
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }
}