<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Order;

use Sonata\Component\Customer\CustomerInterface;

interface OrderInterface
{
    const STATUS_OPEN       = 0; // created but not validated
    const STATUS_PENDING    = 1; // waiting from action from the user
    const STATUS_VALIDATED  = 2; // the order is validated does not mean the payment is ok
    const STATUS_CANCELLED  = 3; // the order is cancelled
    const STATUS_ERROR      = 4; // the order has an error
    const STATUS_STOPPED    = 5; // use if the subscription has been cancelled/stopped

    /**
     * @return integer the order id
     */
    function getId();

    /**
     * Set reference
     *
     * @param string $reference
     */
    function setReference($reference);

    /**
     * Get reference
     *
     * @return string $reference
     */
    function getReference();

    /**
     * Set payment method
     *
     * @param string $paymentMethod
     */
    function setPaymentMethod($paymentMethod);

    /**
     * Get payment method
     *
     * @return string
     */
    function getPaymentMethod();

    /**
     * Set delivery method
     *
     * @param string $deliveryMethod
     */
    function setDeliveryMethod($deliveryMethod);

    /**
     * Get delivery method
     *
     * @return string $deliveryMethod
     */
    function getDeliveryMethod();

    /**
     * Set currency
     *
     * @param string $currency
     */
    function setCurrency($currency);

    /**
     * Get currency
     *
     * @return string $currency
     */
    function getCurrency();

    /**
     * Set status
     *
     * @param integer $status
     */
    function setStatus($status);

    /**
     * Get status
     *
     * @return integer $status
     */
    function getStatus();

    /**
     * Set payment status
     *
     * @param integer $paymentStatus
     */
    function setPaymentStatus($paymentStatus);

    /**
     * Get payment status
     *
     * @return integer $paymentStatus
     */
    function getPaymentStatus();

    /**
     * Set delivery status
     *
     * @param integer $deliveryStatus
     */
    function setDeliveryStatus($deliveryStatus);

    /**
     * Get delivery status
     *
     * @return integer $deliveryStatus
     */
    function getDeliveryStatus();

    /**
     * Set validated at
     *
     * @param datetime $validatedAt
     */
    function setValidatedAt(\DateTime $validatedAt = null);

    /**
     * Get validated at
     *
     * @return datetime $validatedAt
     */
    function getValidatedAt();

    /**
     * Set username
     *
     * @param string $username
     */
    function setUsername($username);

    /**
     * Get username
     *
     * @return string $username
     */
    function getUsername();

    /**
     * Set totalInc
     *
     * @param decimal $totalInc
     */
    function setTotalInc($totalInc);

    /**
     * Get totalInc
     *
     * @return decimal $totalInc
     */
    function getTotalInc();

    /**
     * Set totalExcl
     *
     * @param decimal $totalExcl
     */
    function setTotalExcl($totalExcl);

    /**
     * Get totalExcl
     *
     * @return decimal $totalExcl
     */
    function getTotalExcl();

    /**
     * Set delivery cost
     *
     * @param decimal $deliveryCost
     */
    function setDeliveryCost($deliveryCost);

    /**
     * Get delivery cost
     *
     * @return decimal $deliveryCost
     */
    function getDeliveryCost();

    /**
     * Set billing name
     *
     * @param string $billingName
     */
    function setBillingName($billingName);

    /**
     * Get billing name
     *
     * @return string $billingName
     */
    function getBillingName();

    /**
     * Set billing phone
     *
     * @param string $billingPhone
     */
    function setBillingPhone($billingPhone);

    /**
     * Get billing phone
     *
     * @return string $billingPhone
     */
    function getBillingPhone();

    /**
     * Set billing address1
     *
     * @param string $billingAddress1
     */
    function setBillingAddress1($billingAddress1);

    /**
     * Get billing address1
     *
     * @return string $billingAddress1
     */
    function getBillingAddress1();

    /**
     * Set billing address2
     *
     * @param string $billingAddress2
     */
    function setBillingAddress2($billingAddress2);

    /**
     * Get billing address2
     *
     * @return string $billingAddress2
     */
    function getBillingAddress2();

    /**
     * Set billing address3
     *
     * @param string $billingAddress3
     */
    function setBillingAddress3($billingAddress3);

    /**
     * Get billing address3
     *
     * @return string $billingAddress3
     */
    function getBillingAddress3();

    /**
     * Set billing city
     *
     * @param string $billingCity
     */
    function setBillingCity($billingCity);

    /**
     * Get billing city
     *
     * @return string $billingCity
     */
    function getBillingCity();

    /**
     * Set billing postcode
     *
     * @param string $billingPostcode
     */
    function setBillingPostcode($billingPostcode);

    /**
     * Get billing postcode
     *
     * @return string $billingPostcode
     */
    function getBillingPostcode();

    /**
     * Set billing country code
     *
     * @param string $billingCountry
     */
    function setBillingCountryCode($billingCountryCode);
    /**
     * Get billing country
     *
     * @return string $billingCountryCode
     */
    function getBillingCountryCode();

    /**
     * Set billing fax
     *
     * @param string $billingFax
     */
    function setBillingFax($billingFax);

    /**
     * Get billing fax
     *
     * @return string $billingFax
     */
    function getBillingFax();

    /**
     * Set billing email
     *
     * @param string $billingEmail
     */
    function setBillingEmail($billingEmail);

    /**
     * Get billing email
     *
     * @return string $billingEmail
     */
    function getBillingEmail();

    /**
     * Set billing mobile
     *
     * @param string $billingMobile
     */
    function setBillingMobile($billingMobile);

    /**
     * Get billing mobile
     *
     * @return string $billingMobile
     */
    function getBillingMobile();

    /**
     * Set shipping name
     *
     * @param string $shippingName
     */
    function setShippingName($shippingName);

    /**
     * Get shipping name
     *
     * @return string $shippingName
     */
    function getShippingName();

    /**
     * Set shipping phone
     *
     * @param string $shippingPhone
     */
    function setShippingPhone($shippingPhone);

    /**
     * Get shipping phone
     *
     * @return string $shippingPhone
     */
    function getShippingPhone();

    /**
     * Set shipping address1
     *
     * @param string $shippingAddress1
     */
    function setShippingAddress1($shippingAddress1);

    /**
     * Get shipping address1
     *
     * @return string $shippingAddress1
     */
    function getShippingAddress1();

    /**
     * Set shipping address2
     *
     * @param string $shippingAddress2
     */
    function setShippingAddress2($shippingAddress2);

    /**
     * Get shipping address2
     *
     * @return string $shippingAddress2
     */
    function getShippingAddress2();

    /**
     * Set shipping address3
     *
     * @param string $shippingAddress3
     */
    function setShippingAddress3($shippingAddress3);

    /**
     * Get shipping address3
     *
     * @return string $shippingAddress3
     */
    function getShippingAddress3();

    /**
     * Set shipping city
     *
     * @param string $shippingCity
     */
    function setShippingCity($shippingCity);

    /**
     * Get shipping city
     *
     * @return string $shippingCity
     */
    function getShippingCity();

    /**
     * Set shipping postcode
     *
     * @param string $shippingPostcode
     */
    function setShippingPostcode($shippingPostcode);

    /**
     * Get shipping postcode
     *
     * @return string $shippingPostcode
     */
    function getShippingPostcode();

    /**
     * Set shipping country
     *
     * @param string $shippingCountry
     */
    function setShippingCountryCode($shippingCountryCode);

    /**
     * Get shipping country
     *
     * @return string $shippingCountry
     */
    function getShippingCountryCode();

    /**
     * Set shipping fax
     *
     * @param string $shippingFax
     */
    function setShippingFax($shippingFax);

    /**
     * Get shipping fax
     *
     * @return string $shippingFax
     */
    function getShippingFax();

    /**
     * Set shipping email
     *
     * @param string $shippingEmail
     */
    function setShippingEmail($shippingEmail);

    /**
     * Get shipping email
     *
     * @return string $shippingEmail
     */
    function getShippingEmail();

    /**
     * Set shipping mobile
     *
     * @param string $shippingMobile
     */
    function setShippingMobile($shippingMobile);

    /**
     * Get shipping mobile
     *
     * @return string $shippingMobile
     */
    function getShippingMobile();

    /**
     * @abstract
     * @return array of
     */
    function getOrderElements();

    /**
     * @abstract
     * @param OrderElementInterface $orderElement
     * @return void
     */
    function addOrderElement(OrderElementInterface $orderElement);

    /**
     *
     * return true if the order is validated
     *
     * @return boolean
     */
    function isValidated();

    /**
     *
     *
     * @return boolean true if cancelled, else false
     */
    function isCancelled();

    /**
     *
     *
     * @return boolean true if pending, else false
     */
    function isPending();

    /**
     * Return true if the order is open
     *
     * @return boolean
     */
    function isOpen();

    /**
     * Return true if the order has an error
     *
     * @return boolean
     */
    function isError();

    /**
     * @abstract
     * @param \DateTime|null $createdAt
     * @return void
     */
    function setCreatedAt(\DateTime $createdAt = null);

    /**
     * @abstract
     * @return void
     */
    function getCreatedAt();

    /**
     * @abstract
     * @param \DateTime|null $updatedAt
     * @return void
     */
    function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * @abstract
     * @return void
     */
    function getUpdatedAt();

    /**
     * Add order elements
     *
     * @param OrderElementInterface $orderElements
     */
    function addOrderElements(OrderElementInterface $orderElements);

    /**
     * @abstract
     * @param $orderElements
     * @return array
     */
    function setOrderElements($orderElements);

    /**
     * @abstract
     * @param \Sonata\Component\Customer\CustomerInterface $customer
     * @return void
     */
    function setCustomer(CustomerInterface $customer);

    /**
     * @abstract
     * @return \Sonata\Component\Customer\CustomerInterface
     */
    function getCustomer();
}