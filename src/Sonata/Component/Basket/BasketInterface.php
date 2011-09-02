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
use Sonata\Component\Customer\CustomerInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\Pool;

interface BasketInterface
{

    /**
     * @abstract
     * @param \Sonata\Component\Product\Pool $pool
     * @return void
     */
    function setProductPool(Pool $pool);

    /**
     * @abstract
     * @return \Sonata\Component\Product\Pool
     */
    function getProductPool();

    /**
     * test is the basket has elements
     *
     * @return boolean
     */
    function isEmpty();

    /**
     * Check is the basket is valid : elements, Payment and Delivery information
     *
     * if $element_only is set to true, only elements are checked
     *
     * @param boolean $elementsOnly
     * @return boolean
     */
    function isValid($elementsOnly = false);

    /**
     * set the Delivery method
     *
     * @param \Sonata\Component\Delivery\DeliveryInterface $method
     */
    function setDeliveryMethod(DeliveryInterface $method = null);

    /**
     *
     * @return \Sonata\Component\Delivery\DeliveryInterface
     */
    function getDeliveryMethod();

    /**
     * set the Delivery address
     *
     * @param \Sonata\Component\Customer\AddressInterface $address
     */
    function setDeliveryAddress(AddressInterface $address = null);

    /**
     *
     *
     * @return \Sonata\Component\Customer\AddressInterface
     */
    function getDeliveryAddress();

    /**
     * set Payment method
     *
     * @param \Sonata\Component\Payment\PaymentInterface $method
     */
    function setPaymentMethod(PaymentInterface $method = null);

    /**
     *
     *
     * @return \Sonata\Component\Payment\PaymentInterface
     */
    function getPaymentMethod();

    /**
     * set the Payment address
     *
     * @param \Sonata\Component\Customer\AddressInterface $address
     */
    function setPaymentAddress(AddressInterface $address = null);

    /**
     *
     *
     * @return \Sonata\Component\Customer\AddressInterface
     */
    function getPaymentAddress();

    /**
     * Check if the product can be added to the basket
     *
     * @param \Sonata\Component\Product\ProductInterface $product
     *
     * @return boolean
     */
    function isAddable(ProductInterface $product);

    /**
     * reset basket
     *
     * @abstract
     * @param bool $full
     * @return void
     */
    function reset($full = true);

    /**
     * return BasketElements
     *
     * @return \Sonata\Component\Basket\BasketElementInterface[]
     */
    function getBasketElements();

    /**
     * Warning : this method should be only used by the validation framework
     *
     * @param  $elements
     * @return void
     */
    function setBasketElements($elements);

    /**
     * count number of element in the basket
     *
     * @return integer
     */
    function countBasketElements();

    /**
     * return true if the basket has some elements ...
     *
     * @return boolean
     */
    function hasBasketElements();

    /**
     * return the BasketElement depends on the $product or the position from the element stacks
     *
     * @abstract
     * @param \Sonata\Component\Product\ProductInterface $product
     * @return BasketElementInterface
     */
    function getElement(ProductInterface $product);

    /**
     * delete an element from the basket depend on the $element. Element
     * can be a product or a basket element
     *
     * @param mixed $element
     *
     * @return BasketElementInterface
     */
    function removeElement(BasketElementInterface $element);

    /**
     * Add a basket element into the current basket
     *
     * @param BasketElementInterface $basketElement
     */
    function addBasketElement(BasketElementInterface $basketElement);

    /**
     * return true if the basket has a least one recurrent product (subscription)
     *
     *  @return boolean
     */
    function hasRecurrentPayment();

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
    function getTotal($vat = false, $recurrentOnly = null);

    /**
     * return the VAT of the current basket
     *
     * @return float
     */
    function getVatAmount();
    /**
     * return the Delivery price
     *
     *
     * @param boolean $vat
     * @return float
     */
    function getDeliveryPrice($vat = false);

    /**
     * check if the basket contains $product
     *
     * @param \Sonata\Component\Product\ProductInterface $product
     * @return boolean
     */
    function hasProduct(ProductInterface $product);

    /**
     * Compute the price of the basket
     *
     */
    function buildPrices();

    /**
     * remove basket element market as deleted
     * @return void
     */
    function clean();

    function setDeliveryAddressId($deliveryAddressId);

    function getDeliveryAddressId();

    function setPaymentAddressId($paymentAddressId);

    function getPaymentAddressId();

    function getPaymentMethodCode();

    function getDeliveryMethodCode();

    function setCustomer(CustomerInterface $customer);

    function getCustomer();

    function setCustomerId($customerId);

    function getCustomerId();
}