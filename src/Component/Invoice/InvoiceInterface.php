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
     * @return int
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
     * @param string $paymentMethod
     */
    public function setPaymentMethod($paymentMethod);

    /**
     * @return string
     */
    public function getPaymentMethod();

    /**
     * @param string $reference
     */
    public function setReference($reference);

    /**
     * @return string
     */
    public function getReference();

    /**
     * @param CurrencyInterface $currency
     */
    public function setCurrency(CurrencyInterface $currency);

    /**
     * @return CurrencyInterface
     */
    public function getCurrency();

    /**
     * @param int $status
     */
    public function setStatus($status);

    /**
     * @return int
     */
    public function getStatus();

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
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $phone
     */
    public function setPhone($phone);

    /**
     * @return string
     */
    public function getPhone();

    /**
     * @param string $address1
     */
    public function setAddress1($address1);

    /**
     * @return string
     */
    public function getAddress1();

    /**
     * @param string $address2
     */
    public function setAddress2($address2);

    /**
     * @return string
     */
    public function getAddress2();

    /**
     * @param string $address3
     */
    public function setAddress3($address3);

    /**
     * @return string
     */
    public function getAddress3();

    /**
     * @param string $city
     */
    public function setCity($city);

    /**
     * @return string
     */
    public function getCity();

    /**
     * @param string $postcode
     */
    public function setPostcode($postcode);

    /**
     * @return string
     */
    public function getPostcode();

    /**
     * @param string $country
     */
    public function setCountry($country);

    /**
     * @return string
     */
    public function getCountry();

    /**
     * @param string $fax
     */
    public function setFax($fax);

    /**
     * @return string
     */
    public function getFax();

    /**
     * @param string $email
     */
    public function setEmail($email);

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $mobile
     */
    public function setMobile($mobile);

    /**
     * @return string
     */
    public function getMobile();

    /**
     * @param CustomerInterface $customer
     */
    public function setCustomer(CustomerInterface $customer);

    /**
     * @return CustomerInterface
     */
    public function getCustomer();

    /**
     * @return string
     */
    public function getLocale();

    /**
     * @param string $locale
     */
    public function setLocale($locale);

    /**
     * @return InvoiceElementInterface[]
     */
    public function getInvoiceElements();

    /**
     * @param InvoiceElementInterface $element
     */
    public function addInvoiceElement(InvoiceElementInterface $element);

    /**
     * @param array $elements
     */
    public function setInvoiceElements(array $elements);
}
