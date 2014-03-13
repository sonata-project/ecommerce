<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sonata\InvoiceBundle\Entity;

use Sonata\Component\Invoice\InvoiceInterface;
use Sonata\Component\Currency\CurrencyInterface;
use Sonata\Component\Customer\CustomerInterface;
use Sonata\Component\Invoice\InvoiceElementInterface;
use Sonata\CustomerBundle\Entity\BaseAddress;
use Sonata\UserBundle\Model\UserInterface;

/**
 * Sonata\InvoiceBundle\Entity\BaseInvoice
 */
abstract class BaseInvoice implements InvoiceInterface
{
    /**
     * @var string $reference
     */
    protected $reference;

    /**
     * @var integer $userId
     */
    protected $customer;

    /**
     * @var CurrencyInterface $currency
     */
    protected $currency;

    /**
     * @var integer $status
     */
    protected $status;

    /**
     * @var float $totalInc
     */
    protected $totalInc;

    /**
     * @var float $totalExcl
     */
    protected $totalExcl;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $phone
     */
    protected $phone;

    /**
     * @var string $address1
     */
    protected $address1;

    /**
     * @var string $address2
     */
    protected $address2;

    /**
     * @var string $address3
     */
    protected $address3;

    /**
     * @var string $city
     */
    protected $city;

    /**
     * @var string $postcode
     */
    protected $postcode;

    /**
     * @var string $country
     */
    protected $country;

    /**
     * @var string $fax
     */
    protected $fax;

    /**
     * @var string $email
     */
    protected $email;

    /**
     * @var string $mobile
     */
    protected $mobile;

    /**
     * @var string $paymentMethod
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
    protected $invoiceElements = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Returns formatted billing address
     *
     * @param string $sep
     *
     * @return string
     */
    public function getFullBilling($sep = ", ")
    {
        return BaseAddress::formatAddress($this->getBillingAsArray(), $sep);
    }

    /**
     * @return array
     */
    public function getBillingAsArray()
    {
        return array(
            'firstname'    => $this->getName(),
            'lastname'     => "",
            'address1'     => $this->getAddress1(),
            'address2'     => $this->getAddress2(),
            'address3'     => $this->getAddress3(),
            'postcode'     => $this->getPostcode(),
            'city'         => $this->getCity(),
            'country_code' => $this->getCountry()
        );
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
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

    /**
     * {@inheritdoc}
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Set reference
     *
     * @param string $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * Get reference
     *
     * @return string $reference
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set userId
     *R
     * @param CustomerInterface $customer
     */
    public function setCustomer(CustomerInterface $customer = null)
    {
        $this->customer = $customer;
    }

    /**
     * Get userId
     *
     * @return integer $customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set currency
     *
     * @param CurrencyInterface $currency
     */
    public function setCurrency(CurrencyInterface $currency)
    {
        $this->currency = $currency;
    }

    /**
     * Get currency
     *
     * @return CurrencyInterface $currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set status
     *
     * @param integer $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return integer $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set totalInc
     *
     * @param float $totalInc
     */
    public function setTotalInc($totalInc)
    {
        $this->totalInc = $totalInc;
    }

    /**
     * Get totalInc
     *
     * @return float $totalInc
     */
    public function getTotalInc()
    {
        return $this->totalInc;
    }

    /**
     * Set totalExcl
     *
     * @param float $totalExcl
     */
    public function setTotalExcl($totalExcl)
    {
        $this->totalExcl = $totalExcl;
    }

    /**
     * Get totalExcl
     *
     * @return float $totalExcl
     */
    public function getTotalExcl()
    {
        return $this->totalExcl;
    }

    /**
     * Returns all VAT amounts contained in elements
     *
     * @return array
     */
    public function getVatAmounts()
    {
        $amounts = array();

        foreach ($this->getInvoiceElements() as $invoiceElement) {
            $rate = $invoiceElement->getVatRate();

            if (0 == $rate) {
                continue;
            }

            if (isset($amounts[$rate])) {
                $amounts[$rate]['amount'] = bcadd($amounts[$rate]['amount'], $invoiceElement->getVatAmount());
            } else {
                $amounts[$rate] = array(
                    'rate' => $rate,
                    'amount' => $invoiceElement->getVatAmount()
                );
            }
        }

        return $amounts;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set phone
     *
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * Get phone
     *
     * @return string $phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set address1
     *
     * @param string $address1
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
    }

    /**
     * Get address1
     *
     * @return string $address1
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set address2
     *
     * @param string $address2
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
    }

    /**
     * Get address2
     *
     * @return string $address2
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set address3
     *
     * @param string $address3
     */
    public function setAddress3($address3)
    {
        $this->address3 = $address3;
    }

    /**
     * Get address3
     *
     * @return string $address3
     */
    public function getAddress3()
    {
        return $this->address3;
    }

    /**
     * Set city
     *
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Get city
     *
     * @return string $city
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set postcode
     *
     * @param string $postcode
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;
    }

    /**
     * Get postcode
     *
     * @return string $postcode
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Set country
     *
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * Get country
     *
     * @return string $country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set fax
     *
     * @param string $fax
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
    }

    /**
     * Get fax
     *
     * @return string $fax
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * Get mobile
     *
     * @return string $mobile
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set user
     *
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return UserInterface $user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function getInvoiceElements()
    {
        return $this->invoiceElements;
    }

    /**
     * {@inheritdoc}
     */
    public function addInvoiceElement(InvoiceElementInterface $element)
    {
        $this->invoiceElements[] = $element;
    }

    /**
     * {@inheritdoc}
     */
    public function setInvoiceElements(array $elements)
    {
        $this->invoiceElements = $elements;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getReference() ?: 'n/a';
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
     * Gets the locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Sets the locale
     *
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }


    /**
     * @return array
     */
    public static function getStatusList()
    {
        return array(
            self::STATUS_OPEN     => 'status_open',
            self::STATUS_PAID     => 'status_paid',
            self::STATUS_CONFLICT => 'status_conflict',
        );
    }

    /**
     * @return array
     */
    public static function getValidationStatusList()
    {
        return array_keys(self::getStatusList());
    }
}
