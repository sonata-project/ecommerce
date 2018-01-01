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
     * @var InvoiceElementInterface[]
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
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentMethod($paymentMethod): void
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
     * {@inheritdoc}
     */
    public function setReference($reference): void
    {
        $this->reference = $reference;
    }

    /**
     * {@inheritdoc}
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomer(CustomerInterface $customer = null): void
    {
        $this->customer = $customer;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency(CurrencyInterface $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalInc($totalInc): void
    {
        $this->totalInc = $totalInc;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalInc()
    {
        return $this->totalInc;
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalExcl($totalExcl): void
    {
        $this->totalExcl = $totalExcl;
    }

    /**
     * {@inheritdoc}
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

            if (0 == $rate) {
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
     * {@inheritdoc}
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    /**
     * {@inheritdoc}
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * {@inheritdoc}
     */
    public function setAddress1($address1): void
    {
        $this->address1 = $address1;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * {@inheritdoc}
     */
    public function setAddress2($address2): void
    {
        $this->address2 = $address2;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * {@inheritdoc}
     */
    public function setAddress3($address3): void
    {
        $this->address3 = $address3;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress3()
    {
        return $this->address3;
    }

    /**
     * {@inheritdoc}
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

    /**
     * {@inheritdoc}
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * {@inheritdoc}
     */
    public function setPostcode($postcode): void
    {
        $this->postcode = $postcode;
    }

    /**
     * {@inheritdoc}
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * {@inheritdoc}
     */
    public function setCountry($country): void
    {
        $this->country = $country;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * {@inheritdoc}
     */
    public function setFax($fax): void
    {
        $this->fax = $fax;
    }

    /**
     * {@inheritdoc}
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function setMobile($mobile): void
    {
        $this->mobile = $mobile;
    }

    /**
     * {@inheritdoc}
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }

    /**
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
    public function addInvoiceElement(InvoiceElementInterface $element): void
    {
        $this->invoiceElements[] = $element;
    }

    /**
     * {@inheritdoc}
     */
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
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * {@inheritdoc}
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
