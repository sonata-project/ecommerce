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

namespace Sonata\Component\Order;

use Sonata\Component\Currency\CurrencyInterface;
use Sonata\Component\Customer\CustomerInterface;

interface OrderInterface
{
    public const STATUS_OPEN = 0; // created but not validated
    public const STATUS_PENDING = 1; // waiting from action from the user
    public const STATUS_VALIDATED = 2; // the order is validated does not mean the payment is ok
    public const STATUS_CANCELLED = 3; // the order is cancelled
    public const STATUS_ERROR = 4; // the order has an error
    public const STATUS_STOPPED = 5; // use if the subscription has been cancelled/stopped

    /**
     * @return int the order id
     */
    public function getId();

    /**
     * Set reference.
     *
     * @param string $reference
     */
    public function setReference($reference);

    /**
     * Get reference.
     *
     * @return string $reference
     */
    public function getReference();

    /**
     * Set payment method.
     *
     * @param string $paymentMethod
     */
    public function setPaymentMethod($paymentMethod);

    /**
     * Get payment method.
     *
     * @return string
     */
    public function getPaymentMethod();

    /**
     * Set delivery method.
     *
     * @param string $deliveryMethod
     */
    public function setDeliveryMethod($deliveryMethod);

    /**
     * Get delivery method.
     *
     * @return string $deliveryMethod
     */
    public function getDeliveryMethod();

    /**
     * Set currency.
     *
     * @param CurrencyInterface $currency
     */
    public function setCurrency(CurrencyInterface $currency);

    /**
     * Get currency.
     *
     * @return CurrencyInterface $currency
     */
    public function getCurrency();

    /**
     * @return string
     */
    public function getLocale();

    /**
     * @param string $locale
     */
    public function setLocale($locale);

    /**
     * Set status.
     *
     * @param int $status
     */
    public function setStatus($status);

    /**
     * Get status.
     *
     * @return int $status
     */
    public function getStatus();

    /**
     * Set payment status.
     *
     * @param int $paymentStatus
     */
    public function setPaymentStatus($paymentStatus);

    /**
     * Get payment status.
     *
     * @return int $paymentStatus
     */
    public function getPaymentStatus();

    /**
     * Set delivery status.
     *
     * @param int $deliveryStatus
     */
    public function setDeliveryStatus($deliveryStatus);

    /**
     * Get delivery status.
     *
     * @return int $deliveryStatus
     */
    public function getDeliveryStatus();

    /**
     * Set validated at.
     *
     * @param \Datetime $validatedAt
     */
    public function setValidatedAt(\DateTime $validatedAt = null);

    /**
     * Get validated at.
     *
     * @return \Datetime $validatedAt
     */
    public function getValidatedAt();

    /**
     * Set username.
     *
     * @param string $username
     */
    public function setUsername($username);

    /**
     * Get username.
     *
     * @return string $username
     */
    public function getUsername();

    /**
     * Set totalInc.
     *
     * @param float $totalInc
     */
    public function setTotalInc($totalInc);

    /**
     * Get totalInc.
     *
     * @return float $totalInc
     */
    public function getTotalInc();

    /**
     * Set totalExcl.
     *
     * @param float $totalExcl
     */
    public function setTotalExcl($totalExcl);

    /**
     * Get totalExcl.
     *
     * @return float $totalExcl
     */
    public function getTotalExcl();

    /**
     * Set delivery cost (VAT included).
     *
     * @param float $deliveryCost
     */
    public function setDeliveryCost($deliveryCost);

    /**
     * Get delivery cost.
     *
     * @return float $deliveryCost
     */
    public function getDeliveryCost();

    /**
     * Set delivery VAT.
     *
     * @param float $deliveryVat
     */
    public function setDeliveryVat($deliveryVat);

    /**
     * Get delivery VAT.
     *
     * @return float $deliveryVat
     */
    public function getDeliveryVat();

    /**
     * Set billing name.
     *
     * @param string $billingName
     */
    public function setBillingName($billingName);

    /**
     * Get billing name.
     *
     * @return string $billingName
     */
    public function getBillingName();

    /**
     * Set billing phone.
     *
     * @param string $billingPhone
     */
    public function setBillingPhone($billingPhone);

    /**
     * Get billing phone.
     *
     * @return string $billingPhone
     */
    public function getBillingPhone();

    /**
     * Set billing address1.
     *
     * @param string $billingAddress1
     */
    public function setBillingAddress1($billingAddress1);

    /**
     * Get billing address1.
     *
     * @return string $billingAddress1
     */
    public function getBillingAddress1();

    /**
     * Set billing address2.
     *
     * @param string $billingAddress2
     */
    public function setBillingAddress2($billingAddress2);

    /**
     * Get billing address2.
     *
     * @return string $billingAddress2
     */
    public function getBillingAddress2();

    /**
     * Set billing address3.
     *
     * @param string $billingAddress3
     */
    public function setBillingAddress3($billingAddress3);

    /**
     * Get billing address3.
     *
     * @return string $billingAddress3
     */
    public function getBillingAddress3();

    /**
     * Set billing city.
     *
     * @param string $billingCity
     */
    public function setBillingCity($billingCity);

    /**
     * Get billing city.
     *
     * @return string $billingCity
     */
    public function getBillingCity();

    /**
     * Set billing postcode.
     *
     * @param string $billingPostcode
     */
    public function setBillingPostcode($billingPostcode);

    /**
     * Get billing postcode.
     *
     * @return string $billingPostcode
     */
    public function getBillingPostcode();

    /**
     * Set billing country code.
     *
     * @param string $billingCountryCode
     */
    public function setBillingCountryCode($billingCountryCode);

    /**
     * Get billing country.
     *
     * @return string $billingCountryCode
     */
    public function getBillingCountryCode();

    /**
     * Set billing fax.
     *
     * @param string $billingFax
     */
    public function setBillingFax($billingFax);

    /**
     * Get billing fax.
     *
     * @return string $billingFax
     */
    public function getBillingFax();

    /**
     * Set billing email.
     *
     * @param string $billingEmail
     */
    public function setBillingEmail($billingEmail);

    /**
     * Get billing email.
     *
     * @return string $billingEmail
     */
    public function getBillingEmail();

    /**
     * Set billing mobile.
     *
     * @param string $billingMobile
     */
    public function setBillingMobile($billingMobile);

    /**
     * Get billing mobile.
     *
     * @return string $billingMobile
     */
    public function getBillingMobile();

    /**
     * Set shipping name.
     *
     * @param string $shippingName
     */
    public function setShippingName($shippingName);

    /**
     * Get shipping name.
     *
     * @return string $shippingName
     */
    public function getShippingName();

    /**
     * Set shipping phone.
     *
     * @param string $shippingPhone
     */
    public function setShippingPhone($shippingPhone);

    /**
     * Get shipping phone.
     *
     * @return string $shippingPhone
     */
    public function getShippingPhone();

    /**
     * Set shipping address1.
     *
     * @param string $shippingAddress1
     */
    public function setShippingAddress1($shippingAddress1);

    /**
     * Get shipping address1.
     *
     * @return string $shippingAddress1
     */
    public function getShippingAddress1();

    /**
     * Set shipping address2.
     *
     * @param string $shippingAddress2
     */
    public function setShippingAddress2($shippingAddress2);

    /**
     * Get shipping address2.
     *
     * @return string $shippingAddress2
     */
    public function getShippingAddress2();

    /**
     * Set shipping address3.
     *
     * @param string $shippingAddress3
     */
    public function setShippingAddress3($shippingAddress3);

    /**
     * Get shipping address3.
     *
     * @return string $shippingAddress3
     */
    public function getShippingAddress3();

    /**
     * Set shipping city.
     *
     * @param string $shippingCity
     */
    public function setShippingCity($shippingCity);

    /**
     * Get shipping city.
     *
     * @return string $shippingCity
     */
    public function getShippingCity();

    /**
     * Set shipping postcode.
     *
     * @param string $shippingPostcode
     */
    public function setShippingPostcode($shippingPostcode);

    /**
     * Get shipping postcode.
     *
     * @return string $shippingPostcode
     */
    public function getShippingPostcode();

    /**
     * Set shipping country.
     *
     * @param string $shippingCountryCode
     */
    public function setShippingCountryCode($shippingCountryCode);

    /**
     * Get shipping country.
     *
     * @return string $shippingCountry
     */
    public function getShippingCountryCode();

    /**
     * Set shipping fax.
     *
     * @param string $shippingFax
     */
    public function setShippingFax($shippingFax);

    /**
     * Get shipping fax.
     *
     * @return string $shippingFax
     */
    public function getShippingFax();

    /**
     * Set shipping email.
     *
     * @param string $shippingEmail
     */
    public function setShippingEmail($shippingEmail);

    /**
     * Get shipping email.
     *
     * @return string $shippingEmail
     */
    public function getShippingEmail();

    /**
     * Set shipping mobile.
     *
     * @param string $shippingMobile
     */
    public function setShippingMobile($shippingMobile);

    /**
     * Get shipping mobile.
     *
     * @return string $shippingMobile
     */
    public function getShippingMobile();

    /**
     * @return array
     */
    public function getOrderElements();

    /**
     * @param OrderElementInterface $orderElement
     */
    public function addOrderElement(OrderElementInterface $orderElement);

    /**
     * return true if the order is validated.
     *
     * @return bool
     */
    public function isValidated();

    /**
     * @return bool true if cancelled, else false
     */
    public function isCancelled();

    /**
     * @return bool true if the order can be cancelled
     */
    public function isCancellable();

    /**
     * @return bool true if pending, else false
     */
    public function isPending();

    /**
     * Return true if the order is open.
     *
     * @return bool
     */
    public function isOpen();

    /**
     * Return true if the order has an error.
     *
     * @return bool
     */
    public function isError();

    /**
     * @param \DateTime|null $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param \DateTime|null $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * Add order elements.
     *
     * @param OrderElementInterface $orderElements
     */
    public function addOrderElements(OrderElementInterface $orderElements);

    /**
     * @param array $orderElements
     *
     * @return array
     */
    public function setOrderElements($orderElements);

    /**
     * @param CustomerInterface $customer
     */
    public function setCustomer(CustomerInterface $customer);

    /**
     * @return CustomerInterface
     */
    public function getCustomer();

    /**
     * @return float
     */
    public function getVat();
}
