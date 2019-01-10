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

namespace Sonata\Component\Invoice;

use Sonata\Component\Currency\CurrencyInterface;
use Sonata\Component\Customer\CustomerInterface;

interface InvoiceInterface
{
    public const STATUS_OPEN = 0; // created but not paid
    public const STATUS_PAID = 1; // the invoice has been paid
    public const STATUS_CONFLICT = 2; // there is a conflict about this invoice

    /**
     * Returns id.
     *
     * @return int $id
     */
    public function getId();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt);

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
     * Set name.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Get name.
     *
     * @return string $name
     */
    public function getName();

    /**
     * Set phone.
     *
     * @param string $phone
     */
    public function setPhone($phone);

    /**
     * Get phone.
     *
     * @return string $phone
     */
    public function getPhone();

    /**
     * Set address1.
     *
     * @param string $address1
     */
    public function setAddress1($address1);

    /**
     * Get address1.
     *
     * @return string $address1
     */
    public function getAddress1();

    /**
     * Set address2.
     *
     * @param string $address2
     */
    public function setAddress2($address2);

    /**
     * Get address2.
     *
     * @return string $address2
     */
    public function getAddress2();

    /**
     * Set address3.
     *
     * @param string $address3
     */
    public function setAddress3($address3);

    /**
     * Get address3.
     *
     * @return string $address3
     */
    public function getAddress3();

    /**
     * Set city.
     *
     * @param string $city
     */
    public function setCity($city);

    /**
     * Get city.
     *
     * @return string $city
     */
    public function getCity();

    /**
     * Set postcode.
     *
     * @param string $postcode
     */
    public function setPostcode($postcode);

    /**
     * Get postcode.
     *
     * @return string $postcode
     */
    public function getPostcode();

    /**
     * Set country.
     *
     * @param string $country
     */
    public function setCountry($country);

    /**
     * Get country.
     *
     * @return string $country
     */
    public function getCountry();

    /**
     * Set fax.
     *
     * @param string $fax
     */
    public function setFax($fax);

    /**
     * Get fax.
     *
     * @return string $fax
     */
    public function getFax();

    /**
     * Set email.
     *
     * @param string $email
     */
    public function setEmail($email);

    /**
     * Get email.
     *
     * @return string $email
     */
    public function getEmail();

    /**
     * Set mobile.
     *
     * @param string $mobile
     */
    public function setMobile($mobile);

    /**
     * Get mobile.
     *
     * @return string $mobile
     */
    public function getMobile();

    /**
     * Set user.
     *
     * @param CustomerInterface $customer
     */
    public function setCustomer(CustomerInterface $customer);

    /**
     * Get user.
     *
     * @return CustomerInterface
     */
    public function getCustomer();

    /**
     * Gets the locale.
     *
     * @return string
     */
    public function getLocale();

    /**
     * Sets the locale.
     *
     * @param string $locale
     */
    public function setLocale($locale);

    /**
     * Returns all the invoice elements.
     *
     * @return array
     */
    public function getInvoiceElements();

    /**
     * Adds an invoice element to the invoice.
     *
     * @param InvoiceElementInterface $element
     */
    public function addInvoiceElement(InvoiceElementInterface $element);

    /**
     * Sets the invoice elements collection.
     *
     * @param array $elements
     */
    public function setInvoiceElements(array $elements);
}
