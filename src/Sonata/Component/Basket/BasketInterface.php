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

    function setProductPool(Pool $pool);

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
     * @param boolean $elements_only
     * @return boolean
     */
    function isValid($elementsOnly = false);

    /**
     * set the Delivery method
     *
     * @param Delivery $method
     */
    function setDeliveryMethod(DeliveryInterface $method = null);

    /**
     *
     *
     * @return Delivery
     */
    function getDeliveryMethod();

    /**
     * set the Delivery address
     *
     * @param Address $address
     */
    function setDeliveryAddress(AddressInterface $address = null);

    /**
     *
     *
     * @return Address
     */
    function getDeliveryAddress();

    /**
     * set Payment method
     *
     * @param Payment $method
     */
    function setPaymentMethod(PaymentInterface $method = null);

    /**
     *
     *
     * @return Payment
     */
    function getPaymentMethod();

    /**
     * set the Payment address
     *
     * @param Address $address
     */
    function setPaymentAddress(AddressInterface $address = null);

    /**
     *
     *
     * @return Address
     */
    function getPaymentAddress();

    /**
     * Check if the product can be added to the basket
     *
     * @param Product $product
     *
     * @return boolean
     */
    function isAddable(ProductInterface $product);

    /**
     * reset basket
     *
     */
    function reset($full = true);

    /**
     * return BasketElements
     *
     * @return array BasketElement
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
     * @param mixed $product
     *
     * @return Product
     */
    function getElement(ProductInterface $product);

    /**
     * delete an element from the basket depend on the $element. Element
     * can be a product or a basket element
     *
     * @param mixed $element
     *
     * @return BasketElement
     */
    function removeElement(BasketElementInterface $element);

    /**
     * Add a basket element into the current basket
     *
     * @param BasketElement $basketElement
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
     * @param boolean $tva
     * @param boolean $recurrent_only
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
     * @param Boolean $tva
     * @return float
     */
    function getDeliveryPrice($vat = false);

    /**
     * check if the basket contains $product
     *
     * @param Product $product
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

    function setPaymentMethodCode($paymentMethodCode);

    function getPaymentMethodCode();

    function setCustomer(CustomerInterface $customer);

    function getCustomer();

    function setCustomerId($customerId);

    function getCustomerId();
}