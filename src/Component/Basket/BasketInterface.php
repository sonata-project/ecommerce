<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Basket;

use Sonata\Component\Currency\CurrencyInterface;
use Sonata\Component\Customer\AddressInterface;
use Sonata\Component\Customer\CustomerInterface;
use Sonata\Component\Delivery\ServiceDeliveryInterface;
use Sonata\Component\Payment\PaymentInterface;
use Sonata\Component\Product\Pool;
use Sonata\Component\Product\ProductInterface;

interface BasketInterface
{
    public function setProductPool(Pool $pool);

    /**
     * @return \Sonata\Component\Product\Pool
     */
    public function getProductPool();

    /**
     * test is the basket has elements.
     *
     * @return bool
     */
    public function isEmpty();

    /**
     * Check is the basket is valid : elements, Payment and Delivery information.
     *
     * if $element_only is set to true, only elements are checked
     *
     * @param bool $elementsOnly
     *
     * @return bool
     */
    public function isValid($elementsOnly = false);

    /**
     * set the Delivery method.
     *
     * @param ServiceDeliveryInterface $method
     */
    public function setDeliveryMethod(ServiceDeliveryInterface $method = null);

    /**
     * @return ServiceDeliveryInterface
     */
    public function getDeliveryMethod();

    /**
     * set the Delivery address.
     *
     * @param \Sonata\Component\Customer\AddressInterface $address
     */
    public function setDeliveryAddress(AddressInterface $address = null);

    /**
     * @return \Sonata\Component\Customer\AddressInterface
     */
    public function getDeliveryAddress();

    /**
     * set Payment method.
     *
     * @param \Sonata\Component\Payment\PaymentInterface $method
     */
    public function setPaymentMethod(PaymentInterface $method = null);

    /**
     * @return \Sonata\Component\Payment\PaymentInterface
     */
    public function getPaymentMethod();

    /**
     * set the Payment address.
     *
     * @param \Sonata\Component\Customer\AddressInterface $address
     */
    public function setBillingAddress(AddressInterface $address = null);

    /**
     * @return \Sonata\Component\Customer\AddressInterface
     */
    public function getBillingAddress();

    /**
     * Check if the product can be added to the basket.
     *
     * @return bool
     */
    public function isAddable(ProductInterface $product);

    /**
     * reset basket.
     *
     * @param bool $full
     */
    public function reset($full = true);

    /**
     * return BasketElements.
     *
     * @return \Sonata\Component\Basket\BasketElementInterface[]
     */
    public function getBasketElements();

    /**
     * Warning : this method should be only used by the validation framework.
     *
     * @param array $elements
     */
    public function setBasketElements($elements);

    /**
     * count number of element in the basket.
     *
     * @return int
     */
    public function countBasketElements();

    /**
     * return true if the basket has some elements ...
     *
     * @return bool
     */
    public function hasBasketElements();

    /**
     * return the BasketElement depends on the $product or the position from the element stacks.
     *
     * @return BasketElementInterface
     */
    public function getElement(ProductInterface $product);

    /**
     * deletes several elements from the basket.
     */
    public function removeElements(array $elementsToRemove);

    /**
     * delete an element from the basket depend on the $element. Element
     * can be a product or a basket element.
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
     * can be a product or a basket element.
     *
     * @param mixed $element
     *
     * @return BasketElementInterface
     */
    public function removeBasketElement(BasketElementInterface $element);

    /**
     * Add a basket element into the current basket.
     */
    public function addBasketElement(BasketElementInterface $basketElement);

    /**
     * return true if the basket has a least one recurrent product (subscription).
     *
     *  @return bool
     */
    public function hasRecurrentPayment();

    /**
     * return the total of the basket
     * if $vat = true, return price with vat
     * if $recurrent_only = true, return price for recurrent product only
     * if $recurrent_only = false, return price for non recurrent product only.
     *
     * @param bool $vat           Returns price including VAT?
     * @param bool $recurrentOnly Is recurrent only?
     *
     * @return float
     */
    public function getTotal($vat = false, $recurrentOnly = null);

    /**
     * Returns the VAT of the current basket.
     *
     * @return float
     */
    public function getVatAmount();

    /**
     * Returns an array with all VAT amounts of the current basket.
     *
     * @return array
     */
    public function getVatAmounts();

    /**
     * return the Delivery price.
     *
     * @param bool $vat
     *
     * @return float
     */
    public function getDeliveryPrice($vat = false);

    /**
     * returns the Delivery VAT rate.
     *
     * @return float
     */
    public function getDeliveryVat();

    /**
     * check if the basket contains $product.
     *
     * @return bool
     */
    public function hasProduct(ProductInterface $product);

    /**
     * Compute the price of the basket.
     */
    public function buildPrices();

    /**
     * remove basket element market as deleted.
     */
    public function clean();

    /**
     * @param int $deliveryAddressId
     */
    public function setDeliveryAddressId($deliveryAddressId);

    /**
     * @return int
     */
    public function getDeliveryAddressId();

    /**
     * @param int $billingAddressId
     */
    public function setBillingAddressId($billingAddressId);

    /**
     * @return int
     */
    public function getBillingAddressId();

    /**
     * @param string $paymentMethodCode
     */
    public function setPaymentMethodCode($paymentMethodCode);

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
     * @param int $customerId
     */
    public function setCustomerId($customerId);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @return string
     */
    public function getLocale();

    /**
     * @param string $locale
     */
    public function setLocale($locale);

    /**
     * @return CurrencyInterface
     */
    public function getCurrency();

    public function setCurrency(CurrencyInterface $currency);

    /**
     * @return array
     */
    public function getPositions();

    /**
     * Retrieves fields and associated values use for serialization
     * Used by serialize method.
     *
     * @return array
     */
    public function getSerializationFields();

    /**
     * Retrieves fields for deserialization
     * Used by unserialize method.
     *
     * @return array
     */
    public function getUnserializationFields();
}
