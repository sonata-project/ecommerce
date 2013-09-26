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
use Sonata\Component\Currency\CurrencyInterface;

interface BasketInterface
{

    /**
     * @param  \Sonata\Component\Product\Pool $pool
     */
    public function setProductPool(Pool $pool);

    /**
     * @return \Sonata\Component\Product\Pool
     */
    public function getProductPool();

    /**
     * test is the basket has elements
     *
     * @return boolean
     */
    public function isEmpty();

    /**
     * Check is the basket is valid : elements, Payment and Delivery information
     *
     * if $element_only is set to true, only elements are checked
     *
     * @param  boolean $elementsOnly
     *
     * @return boolean
     */
    public function isValid($elementsOnly = false);

    /**
     * set the Delivery method
     *
     * @param \Sonata\Component\Delivery\DeliveryInterface $method
     */
    public function setDeliveryMethod(DeliveryInterface $method = null);

    /**
     *
     * @return \Sonata\Component\Delivery\DeliveryInterface
     */
    public function getDeliveryMethod();

    /**
     * set the Delivery address
     *
     * @param \Sonata\Component\Customer\AddressInterface $address
     */
    public function setDeliveryAddress(AddressInterface $address = null);

    /**
     *
     *
     * @return \Sonata\Component\Customer\AddressInterface
     */
    public function getDeliveryAddress();

    /**
     * set Payment method
     *
     * @param \Sonata\Component\Payment\PaymentInterface $method
     */
    public function setPaymentMethod(PaymentInterface $method = null);

    /**
     *
     *
     * @return \Sonata\Component\Payment\PaymentInterface
     */
    public function getPaymentMethod();

    /**
     * set the Payment address
     *
     * @param \Sonata\Component\Customer\AddressInterface $address
     */
    public function setPaymentAddress(AddressInterface $address = null);

    /**
     *
     *
     * @return \Sonata\Component\Customer\AddressInterface
     */
    public function getPaymentAddress();

    /**
     * Check if the product can be added to the basket
     *
     * @param \Sonata\Component\Product\ProductInterface $product
     *
     * @return boolean
     */
    public function isAddable(ProductInterface $product);

    /**
     * reset basket
     *
     * @param boolean $full
     */
    public function reset($full = true);

    /**
     * return BasketElements
     *
     * @return \Sonata\Component\Basket\BasketElementInterface[]
     */
    public function getBasketElements();

    /**
     * Warning : this method should be only used by the validation framework
     *
     * @param array $elements
     */
    public function setBasketElements($elements);

    /**
     * count number of element in the basket
     *
     * @return integer
     */
    public function countBasketElements();

    /**
     * return true if the basket has some elements ...
     *
     * @return boolean
     */
    public function hasBasketElements();

    /**
     * return the BasketElement depends on the $product or the position from the element stacks
     *
     * @param \Sonata\Component\Product\ProductInterface $product
     *
     * @return BasketElementInterface
     */
    public function getElement(ProductInterface $product);

    /**
     * delete an element from the basket depend on the $element. Element
     * can be a product or a basket element
     *
     * @deprecated Use RemoveBasketElement instead
     *
     * @param mixed $element
     *
     * @return BasketElementInterface
     */
    public function removeElement(BasketElementInterface $element);

    /**
     * delete an element from the basket depend on the $element. Element
     * can be a product or a basket element
     *
     * @param mixed $element
     *
     * @return BasketElementInterface
     */
    public function removeBasketElement(BasketElementInterface $element);

    /**
     * Add a basket element into the current basket
     *
     * @param BasketElementInterface $basketElement
     */
    public function addBasketElement(BasketElementInterface $basketElement);

    /**
     * return true if the basket has a least one recurrent product (subscription)
     *
     *  @return boolean
     */
    public function hasRecurrentPayment();

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
    public function getTotal($vat = false, $recurrentOnly = null);

    /**
     * return the VAT of the current basket
     *
     * @return float
     */
    public function getVatAmount();
    /**
     * return the Delivery price
     *
     * @param  boolean $vat
     *
     * @return float
     */
    public function getDeliveryPrice($vat = false);

    /**
     * check if the basket contains $product
     *
     * @param  \Sonata\Component\Product\ProductInterface $product
     *
     * @return boolean
     */
    public function hasProduct(ProductInterface $product);

    /**
     * Compute the price of the basket
     *
     */
    public function buildPrices();

    /**
     * remove basket element market as deleted
     */
    public function clean();

    /**
     * @param integer $deliveryAddressId
     */
    public function setDeliveryAddressId($deliveryAddressId);

    /**
     * @return integer
     */
    public function getDeliveryAddressId();

    /**
     * @param integer $paymentAddressId
     */
    public function setPaymentAddressId($paymentAddressId);

    /**
     * @return integer
     */
    public function getPaymentAddressId();

    /**
     * @return string
     */
    public function getPaymentMethodCode();

    /**
     * @return string
     */
    public function getDeliveryMethodCode();

    /**
     * @param \Sonata\Component\Customer\CustomerInterface $customer
     */
    public function setCustomer(CustomerInterface $customer = null);

    /**
     * @param \Sonata\Component\Customer\CustomerInterface
     */
    public function getCustomer();

    /**
     * @param integer $customerId
     */
    public function setCustomerId($customerId);

    /**
     * @return integer
     */
    public function getCustomerId();

    /**
     * @return string
     */
    public function getLocale();

    /**
     * @param  string $locale
     */
    public function setLocale($locale);

    /**
     * @return CurrencyInterface
     */
    public function getCurrency();

    /**
     * @param  CurrencyInterface $currency
     */
    public function setCurrency(CurrencyInterface $currency);
}
