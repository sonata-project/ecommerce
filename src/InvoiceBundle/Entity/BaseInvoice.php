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

namespace Sonata\InvoiceBundle\Entity;

use Sonata\Component\Currency\CurrencyInterface;
use Sonata\Component\Customer\CustomerInterface;
use Sonata\Component\Invoice\InvoiceElementInterface;
use Sonata\Component\Invoice\InvoiceInterface;
use Sonata\CustomerBundle\Entity\BaseAddress;
use Sonata\UserBundle\Model\UserInterface;

abstract class BaseInvoice implements InvoiceInterface
{
    /**
     * @var string
     */
    protected $reference;

    /**
     * @var int
     */
    protected $customer;

    /**
     * @var CurrencyInterface
     */
    protected $currency;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var float
     */
    protected $totalInc;

    /**
     * @var float
     */
    protected $totalExcl;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var string
     */
    protected $address1;

    /**
     * @var string
     */
    protected $address2;

    /**
     * @var string
     */
    protected $address3;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $postcode;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var string
     */
    protected $fax;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $mobile;

    /**
     * @var string
     */
    protected $paymentMethod;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var array
     */
    protected $invoiceElements = [];

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getReference() ?: 'n/a';
    }

    /**
     * Returns formatted billing address.
     *
     * @param string $sep
     *
     * @return string
     */
    public function getFullBilling($sep = ', ')
    {
        return BaseAddress::formatAddress($this->getBillingAsArray(), $sep);
    }

    /**
     * @return array
     */
    public function getBillingAsArray()
    {
        return [
            'firstname' => $this->getName(),
            'lastname' => '',
            'address1' => $this->getAddress1(),
            'address2' => $this->getAddress2(),
            'address3' => $this->getAddress3(),
            'postcode' => $this->getPostcode(),
            'city' => $this->getCity(),
            'country_code' => $this->getCountry(),
        ];
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setPaymentMethod($paymentMethod): void
    {
        $this->paymentMethod = $paymentMethod;
    }

    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Set reference.
     *
     * @param string $reference
     */
    public function setReference($reference): void
    {
        $this->reference = $reference;
    }

    /**
     * Get reference.
     *
     * @return string $reference
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set userId
     *R.
     *
     * @param CustomerInterface $customer
     */
    public function setCustomer(CustomerInterface $customer = null): void
    {
        $this->customer = $customer;
    }

    /**
     * Get userId.
     *
     * @return int $customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set currency.
     *
     * @param CurrencyInterface $currency
     */
    public function setCurrency(CurrencyInterface $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * Get currency.
     *
     * @return CurrencyInterface $currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set status.
     *
     * @param int $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * Get status.
     *
     * @return int $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set totalInc.
     *
     * @param float $totalInc
     */
    public function setTotalInc($totalInc): void
    {
        $this->totalInc = $totalInc;
    }

    /**
     * Get totalInc.
     *
     * @return float $totalInc
     */
    public function getTotalInc()
    {
        return $this->totalInc;
    }

    /**
     * Set totalExcl.
     *
     * @param float $totalExcl
     */
    public function setTotalExcl($totalExcl): void
    {
        $this->totalExcl = $totalExcl;
    }

    /**
     * Get totalExcl.
     *
     * @return float $totalExcl
     */
    public function getTotalExcl()
    {
        return $this->totalExcl;
    }

    /**
     * Returns all VAT amounts contained in elements.
     *
     * @return array
     */
    public function getVatAmounts()
    {
        $amounts = [];

        foreach ($this->getInvoiceElements() as $invoiceElement) {
            $rate = $invoiceElement->getVatRate();
            $amount = (string) $invoiceElement->getVatAmount();

            if (0 === $rate) {
                continue;
            }

            if (isset($amounts[$rate])) {
                $amounts[$rate]['amount'] = bcadd($amounts[$rate]['amount'], $amount);
            } else {
                $amounts[$rate] = [
                    'rate' => $rate,
                    'amount' => $amount,
                ];
            }
        }

        return $amounts;
    }

    /**
     * Set name.
     *
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * Get name.
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set phone.
     *
     * @param string $phone
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    /**
     * Get phone.
     *
     * @return string $phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set address1.
     *
     * @param string $address1
     */
    public function setAddress1($address1): void
    {
        $this->address1 = $address1;
    }

    /**
     * Get address1.
     *
     * @return string $address1
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set address2.
     *
     * @param string $address2
     */
    public function setAddress2($address2): void
    {
        $this->address2 = $address2;
    }

    /**
     * Get address2.
     *
     * @return string $address2
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set address3.
     *
     * @param string $address3
     */
    public function setAddress3($address3): void
    {
        $this->address3 = $address3;
    }

    /**
     * Get address3.
     *
     * @return string $address3
     */
    public function getAddress3()
    {
        return $this->address3;
    }

    /**
     * Set city.
     *
     * @param string $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

    /**
     * Get city.
     *
     * @return string $city
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set postcode.
     *
     * @param string $postcode
     */
    public function setPostcode($postcode): void
    {
        $this->postcode = $postcode;
    }

    /**
     * Get postcode.
     *
     * @return string $postcode
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Set country.
     *
     * @param string $country
     */
    public function setCountry($country): void
    {
        $this->country = $country;
    }

    /**
     * Get country.
     *
     * @return string $country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set fax.
     *
     * @param string $fax
     */
    public function setFax($fax): void
    {
        $this->fax = $fax;
    }

    /**
     * Get fax.
     *
     * @return string $fax
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set email.
     *
     * @param string $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * Get email.
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set mobile.
     *
     * @param string $mobile
     */
    public function setMobile($mobile): void
    {
        $this->mobile = $mobile;
    }

    /**
     * Get mobile.
     *
     * @return string $mobile
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set user.
     *
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }

    /**
     * Get user.
     *
     * @return UserInterface $user
     */
    public function getUser()
    {
        return $this->user;
    }

    public function getInvoiceElements()
    {
        return $this->invoiceElements;
    }

    public function addInvoiceElement(InvoiceElementInterface $element): void
    {
        $this->invoiceElements[] = $element;
    }

    public function setInvoiceElements(array $elements): void
    {
        $this->invoiceElements = $elements;
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        $statusList = self::getStatusList();

        return $statusList[$this->getStatus()];
    }

    /**
     * Gets the locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Sets the locale.
     *
     * @param string $locale
     */
    public function setLocale($locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_OPEN => 'status_open',
            self::STATUS_PAID => 'status_paid',
            self::STATUS_CONFLICT => 'status_conflict',
        ];
    }

    /**
     * @return array
     */
    public static function getValidationStatusList()
    {
        return array_keys(self::getStatusList());
    }
}
