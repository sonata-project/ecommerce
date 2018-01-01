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
     * @return int
     */
    public function getId();

    /**
     * @param string $reference
     */
    public function setReference($reference);

    /**
     * @return string
     */
    public function getReference();

    /**
     * @param string $paymentMethod
     */
    public function setPaymentMethod($paymentMethod);

    /**
     * @return string
     */
    public function getPaymentMethod();

    /**
     * @param string $deliveryMethod
     */
    public function setDeliveryMethod($deliveryMethod);

    /**
     * @return string
     */
    public function getDeliveryMethod();

    /**
     * @param CurrencyInterface $currency
     */
    public function setCurrency(CurrencyInterface $currency);

    /**
     * @return CurrencyInterface
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
     * @param int $status
     */
    public function setStatus($status);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $paymentStatus
     */
    public function setPaymentStatus($paymentStatus);

    /**
     * @return int
     */
    public function getPaymentStatus();

    /**
     * @param int $deliveryStatus
     */
    public function setDeliveryStatus($deliveryStatus);

    /**
     * @return int
     */
    public function getDeliveryStatus();

    /**
     * @param \Datetime|null $validatedAt
     */
    public function setValidatedAt(\DateTime $validatedAt = null);

    /**
     * @return \Datetime
     */
    public function getValidatedAt();

    /**
     * @param string $username
     */
    public function setUsername($username);

    /**
     * @return string
     */
    public function getUsername();

    /**
     * @param float $totalInc
     */
    public function setTotalInc($totalInc);

    /**
     * @return float
     */
    public function getTotalInc();

    /**
     * @param float $totalExcl
     */
    public function setTotalExcl($totalExcl);

    /**
     * @return float
     */
    public function getTotalExcl();

    /**
     * Set delivery cost (VAT included).
     *
     * @param float $deliveryCost
     */
    public function setDeliveryCost($deliveryCost);

    /**
     * @return float
     */
    public function getDeliveryCost();

    /**
     * @param float $deliveryVat
     */
    public function setDeliveryVat($deliveryVat);

    /**
     * @return float
     */
    public function getDeliveryVat();

    /**
     * @param string $billingName
     */
    public function setBillingName($billingName);

    /**
     * @return string
     */
    public function getBillingName();

    /**
     * @param string $billingPhone
     */
    public function setBillingPhone($billingPhone);

    /**
     * @return string
     */
    public function getBillingPhone();

    /**
     * @param string $billingAddress1
     */
    public function setBillingAddress1($billingAddress1);

    /**
     * @return string
     */
    public function getBillingAddress1();

    /**
     * @param string $billingAddress2
     */
    public function setBillingAddress2($billingAddress2);

    /**
     * @return string
     */
    public function getBillingAddress2();

    /**
     * @param string $billingAddress3
     */
    public function setBillingAddress3($billingAddress3);

    /**
     * @return string
     */
    public function getBillingAddress3();

    /**
     * @param string $billingCity
     */
    public function setBillingCity($billingCity);

    /**
     * @return string
     */
    public function getBillingCity();

    /**
     * @param string $billingPostcode
     */
    public function setBillingPostcode($billingPostcode);

    /**
     * @return string
     */
    public function getBillingPostcode();

    /**
     * @param string $billingCountryCode
     */
    public function setBillingCountryCode($billingCountryCode);

    /**
     * @return string
     */
    public function getBillingCountryCode();

    /**
     * @param string $billingFax
     */
    public function setBillingFax($billingFax);

    /**
     * @return string
     */
    public function getBillingFax();

    /**
     * @param string $billingEmail
     */
    public function setBillingEmail($billingEmail);

    /**
     * @return string
     */
    public function getBillingEmail();

    /**
     * @param string $billingMobile
     */
    public function setBillingMobile($billingMobile);

    /**
     * @return string
     */
    public function getBillingMobile();

    /**
     * @param string $shippingName
     */
    public function setShippingName($shippingName);

    /**
     * @return string
     */
    public function getShippingName();

    /**
     * @param string $shippingPhone
     */
    public function setShippingPhone($shippingPhone);

    /**
     * @return string
     */
    public function getShippingPhone();

    /**
     * @param string $shippingAddress1
     */
    public function setShippingAddress1($shippingAddress1);

    /**
     * @return string
     */
    public function getShippingAddress1();

    /**
     * @param string $shippingAddress2
     */
    public function setShippingAddress2($shippingAddress2);

    /**
     * @return string
     */
    public function getShippingAddress2();

    /**
     * @param string $shippingAddress3
     */
    public function setShippingAddress3($shippingAddress3);

    /**
     * @return string
     */
    public function getShippingAddress3();

    /**
     * @param string $shippingCity
     */
    public function setShippingCity($shippingCity);

    /**
     * @return string
     */
    public function getShippingCity();

    /**
     * @param string $shippingPostcode
     */
    public function setShippingPostcode($shippingPostcode);

    /**
     * @return string
     */
    public function getShippingPostcode();

    /**
     * @param string $shippingCountryCode
     */
    public function setShippingCountryCode($shippingCountryCode);

    /**
     * @return string
     */
    public function getShippingCountryCode();

    /**
     * @param string $shippingFax
     */
    public function setShippingFax($shippingFax);

    /**
     * @return string
     */
    public function getShippingFax();

    /**
     * @param string $shippingEmail
     */
    public function setShippingEmail($shippingEmail);

    /**
     * @return string
     */
    public function getShippingEmail();

    /**
     * @param string $shippingMobile
     */
    public function setShippingMobile($shippingMobile);

    /**
     * @return string
     */
    public function getShippingMobile();

    /**
     * @return OrderElementInterface[]
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
     * @param OrderElementInterface $orderElements
     */
    public function addOrderElements(OrderElementInterface $orderElements);

    /**
     * @param array $orderElements
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
