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
     * Set payment_method
     *
     * @param string $payment_method
     */
    function setPaymentMethod($paymentMethod);

    /**
     * Get payment_method
     *
     * @return string $payment_method
     */
    function getPaymentMethod();

    /**
     * Set delivery_method
     *
     * @param string $deliveryMethod
     */
    function setDeliveryMethod($deliveryMethod);

    /**
     * Get delivery_method
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
     * Set payment_status
     *
     * @param integer $paymentStatus
     */
    function setPaymentStatus($paymentStatus);

    /**
     * Get payment_status
     *
     * @return integer $paymentStatus
     */
    function getPaymentStatus();

    /**
     * Set delivery_status
     *
     * @param integer $deliveryStatus
     */
    function setDeliveryStatus($deliveryStatus);

    /**
     * Get delivery_status
     *
     * @return integer $deliveryStatus
     */
    function getDeliveryStatus();

    /**
     * Set validated_at
     *
     * @param datetime $validatedAt
     */
    function setValidatedAt(\DateTime $validatedAt = null);

    /**
     * Get validated_at
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
     * Set delivery_cost
     *
     * @param decimal $deliveryCost
     */
    function setDeliveryCost($deliveryCost);

    /**
     * Get delivery_cost
     *
     * @return decimal $deliveryCost
     */
    function getDeliveryCost();

    /**
     * Set billing_name
     *
     * @param string $billingName
     */
    function setBillingName($billingName);

    /**
     * Get billing_name
     *
     * @return string $billingName
     */
    function getBillingName();

    /**
     * Set billing_phone
     *
     * @param string $billingPhone
     */
    function setBillingPhone($billingPhone);

    /**
     * Get billing_phone
     *
     * @return string $billingPhone
     */
    function getBillingPhone();

    /**
     * Set billing_address1
     *
     * @param string $billingAddress1
     */
    function setBillingAddress1($billingAddress1);

    /**
     * Get billing_address1
     *
     * @return string $billingAddress1
     */
    function getBillingAddress1();

    /**
     * Set billing_address2
     *
     * @param string $billingAddress2
     */
    function setBillingAddress2($billingAddress2);

    /**
     * Get billing_address2
     *
     * @return string $billingAddress2
     */
    function getBillingAddress2();

    /**
     * Set billing_address3
     *
     * @param string $billingAddress3
     */
    function setBillingAddress3($billingAddress3);

    /**
     * Get billing_address3
     *
     * @return string $billingAddress3
     */
    function getBillingAddress3();

    /**
     * Set billing_city
     *
     * @param string $billingCity
     */
    function setBillingCity($billingCity);

    /**
     * Get billing_city
     *
     * @return string $billingCity
     */
    function getBillingCity();

    /**
     * Set billing_postcode
     *
     * @param string $billingPostcode
     */
    function setBillingPostcode($billingPostcode);

    /**
     * Get billing_postcode
     *
     * @return string $billingPostcode
     */
    function getBillingPostcode();

    /**
     * Set billing_country_code
     *
     * @param string $billingCountry
     */
    function setBillingCountryCode($billingCountryCode);
    /**
     * Get billing_country
     *
     * @return string $billingCountryCode
     */
    function getBillingCountryCode();

    /**
     * Set billing_fax
     *
     * @param string $billingFax
     */
    function setBillingFax($billingFax);

    /**
     * Get billing_fax
     *
     * @return string $billingFax
     */
    function getBillingFax();

    /**
     * Set billing_email
     *
     * @param string $billingEmail
     */
    function setBillingEmail($billingEmail);

    /**
     * Get billing_email
     *
     * @return string $billingEmail
     */
    function getBillingEmail();

    /**
     * Set billing_mobile
     *
     * @param string $billingMobile
     */
    function setBillingMobile($billingMobile);

    /**
     * Get billing_mobile
     *
     * @return string $billingMobile
     */
    function getBillingMobile();

    /**
     * Set shipping_name
     *
     * @param string $shippingName
     */
    function setShippingName($shippingName);

    /**
     * Get shipping_name
     *
     * @return string $shippingName
     */
    function getShippingName();

    /**
     * Set shipping_phone
     *
     * @param string $shippingPhone
     */
    function setShippingPhone($shippingPhone);

    /**
     * Get shipping_phone
     *
     * @return string $shippingPhone
     */
    function getShippingPhone();

    /**
     * Set shipping_address1
     *
     * @param string $shippingAddress1
     */
    function setShippingAddress1($shippingAddress1);

    /**
     * Get shipping_address1
     *
     * @return string $shippingAddress1
     */
    function getShippingAddress1();

    /**
     * Set shipping_address2
     *
     * @param string $shippingAddress2
     */
    function setShippingAddress2($shippingAddress2);

    /**
     * Get shipping_address2
     *
     * @return string $shippingAddress2
     */
    function getShippingAddress2();

    /**
     * Set shipping_address3
     *
     * @param string $shippingAddress3
     */
    function setShippingAddress3($shippingAddress3);

    /**
     * Get shipping_address3
     *
     * @return string $shippingAddress3
     */
    function getShippingAddress3();

    /**
     * Set shipping_city
     *
     * @param string $shippingCity
     */
    function setShippingCity($shippingCity);

    /**
     * Get shipping_city
     *
     * @return string $shippingCity
     */
    function getShippingCity();

    /**
     * Set shipping_postcode
     *
     * @param string $shippingPostcode
     */
    function setShippingPostcode($shippingPostcode);

    /**
     * Get shipping_postcode
     *
     * @return string $shippingPostcode
     */
    function getShippingPostcode();

    /**
     * Set shipping_country
     *
     * @param string $shippingCountry
     */
    function setShippingCountryCode($shippingCountryCode);

    /**
     * Get shipping_country
     *
     * @return string $shippingCountry
     */
    function getShippingCountryCode();

    /**
     * Set shipping_fax
     *
     * @param string $shippingFax
     */
    function setShippingFax($shippingFax);

    /**
     * Get shipping_fax
     *
     * @return string $shippingFax
     */
    function getShippingFax();

    /**
     * Set shipping_email
     *
     * @param string $shippingEmail
     */
    function setShippingEmail($shippingEmail);

    /**
     * Get shipping_email
     *
     * @return string $shippingEmail
     */
    function getShippingEmail();

    /**
     * Set shipping_mobile
     *
     * @param string $shippingMobile
     */
    function setShippingMobile($shippingMobile);

    /**
     * Get shipping_mobile
     *
     * @return string $shippingMobile
     */
    function getShippingMobile();

    /**
     * Set user
     *
     * @param Application\Sonata\UserBundle\Entity\User $user
     */
    function setUser($user);

    /**
     * Get user
     *
     * @return Application\Sonata\UserBundle\Entity\User $user
     */
    function getUser();

    function getOrderElements();

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

    function setCreatedAt(\DateTime $createdAt = null);

    function getCreatedAt();

    function setUpdatedAt(\DateTime $updatedAt = null);

    function getUpdatedAt();

    /**
     * Add order_elements
     *
     * @param OrderElementInterface $orderElements
     */
    function addOrderElements(OrderElementInterface $orderElements);

    function setOrderElements($orderElements);

    /**
     * @abstract
     * @param CustomerInterface $customer
     * @return void
     */
    function setCustomer(CustomerInterface $customer);

    /**
     * @abstract
     * @return CustomerInterface
     */
    function getCustomer();
}