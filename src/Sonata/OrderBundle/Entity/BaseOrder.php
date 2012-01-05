<?php

namespace Sonata\OrderBundle\Entity;

use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Order\OrderElementInterface;
use Sonata\Component\Payment\PaymentInterface;
use Sonata\Component\Delivery\DeliveryInterface;
use Sonata\Component\Customer\CustomerInterface;

use Application\Sonata\PaymentBundle\Entity\Transaction;

/**
 * Sonata\OrderBundle\Entity\BaseOrder
 */
abstract class BaseOrder implements OrderInterface
{
    /**
     * @var string $reference
     */
    protected $reference;

    /**
     * @var string $payment_method
     */
    protected $paymentMethod;

    /**
     * @var string $deliveryMethod
     */
    protected $deliveryMethod;

    /**
     * @var string $currency
     */
    protected $currency;

    /**
     * @var integer $status
     */
    protected $status;

    /**
     * @var integer $payment_status
     */
    protected $paymentStatus;

    /**
     * @var integer $delivery_status
     */
    protected $deliveryStatus;

    /**
     * @var datetime $validated_at
     */
    protected $validatedAt;

    /**
     * @var string $username
     */
    protected $username;

    /**
     * @var decimal $totalInc
     */
    protected $totalInc;

    /**
     * @var decimal $totalExcl
     */
    protected $totalExcl;

    /**
     * @var decimal $delivery_cost
     */
    protected $deliveryCost;

    /**
     * @var string $billing_name
     */
    protected $billingName;

    /**
     * @var string $billing_phone
     */
    protected $billingPhone;

    /**
     * @var string $billingAddress1
     */
    protected $billingAddress1;

    /**
     * @var string $billingAddress2
     */
    protected $billingAddress2;

    /**
     * @var string $billingAddress3
     */
    protected $billingAddress3;

    /**
     * @var string $billing_city
     */
    protected $billingCity;

    /**
     * @var string $billing_postcode
     */
    protected $billingPostcode;

    /**
     * @var string $billing_country
     */
    protected $billingCountryCode;

    /**
     * @var string $billing_fax
     */
    protected $billingFax;

    /**
     * @var string $billing_email
     */
    protected $billingEmail;

    /**
     * @var string $billing_mobile
     */
    protected $billingMobile;

    /**
     * @var string $shipping_name
     */
    protected $shippingName;

    /**
     * @var string $shipping_phone
     */
    protected $shippingPhone;

    /**
     * @var string $shipping_address1
     */
    protected $shippingAddress1;

    /**
     * @var string $shipping_address2
     */
    protected $shippingAddress2;

    /**
     * @var string $shipping_address3
     */
    protected $shippingAddress3;

    /**
     * @var string $shipping_city
     */
    protected $shippingCity;

    /**
     * @var string $shipping_postcode
     */
    protected $shippingPostcode;

    /**
     * @var string $shipping_country
     */
    protected $shippingCountryCode;

    /**
     * @var string $shipping_fax
     */
    protected $shippingFax;

    /**
     * @var string $shipping_email
     */
    protected $shippingEmail;

    /**
     * @var string $shipping_mobile
     */
    protected $shippingMobile;

    protected $orderElements;


    protected $createdAt;

    protected $updatedAt;

    protected $customer;


    public function __construct()
    {
        $this->orderElements     = new \Doctrine\Common\Collections\ArrayCollection;
        $this->createdAt         = new \DateTime;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getReference() ?: 'n/a';
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
     * Set payment_method
     *
     * @param string $payment_method
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * Get payment_method
     *
     * @return string $payment_method
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Set delivery_method
     *
     * @param string $deliveryMethod
     */
    public function setDeliveryMethod($deliveryMethod)
    {
        $this->deliveryMethod = $deliveryMethod;
    }

    /**
     * Get delivery_method
     *
     * @return string $deliveryMethod
     */
    public function getDeliveryMethod()
    {
        return $this->deliveryMethod;
    }

    /**
     * Set currency
     *
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * Get currency
     *
     * @return string $currency
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
     * Set payment_status
     *
     * @param integer $paymentStatus
     */
    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;
    }

    /**
     * Get payment_status
     *
     * @return integer $paymentStatus
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * @return string
     */
    public function getPaymentStatusName()
    {
        $statusList = Transaction::getStatusList();
        return $statusList[$this->getPaymentStatus()];
    }

    /**
     * Set delivery_status
     *
     * @param integer $deliveryStatus
     */
    public function setDeliveryStatus($deliveryStatus)
    {
        $this->deliveryStatus = $deliveryStatus;
    }

    /**
     * Get delivery_status
     *
     * @return integer $deliveryStatus
     */
    public function getDeliveryStatus()
    {
        return $this->deliveryStatus;
    }

    /**
     * @return string
     */
    public function getDeliveryStatusName()
    {
        $statusList = self::getStatusList();
        return $statusList[$this->getDeliveryStatus()];
    }

    /**
     * Set validated_at
     *
     * @param \DateTime $validatedAt
     */
    public function setValidatedAt(\DateTime $validatedAt = null)
    {
        $this->validatedAt = $validatedAt;
    }

    /**
     * Get validated_at
     *
     * @return datetime $validatedAt
     */
    public function getValidatedAt()
    {
        return $this->validatedAt;
    }

    /**
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get username
     *
     * @return string $username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set totalInc
     *
     * @param decimal $totalInc
     */
    public function setTotalInc($totalInc)
    {
        $this->totalInc = $totalInc;
    }

    /**
     * Get totalInc
     *
     * @return decimal $totalInc
     */
    public function getTotalInc()
    {
        return $this->totalInc;
    }

    /**
     * Set totalExcl
     *
     * @param decimal $totalExcl
     */
    public function setTotalExcl($totalExcl)
    {
        $this->totalExcl = $totalExcl;
    }

    /**
     * Get totalExcl
     *
     * @return decimal $totalExcl
     */
    public function getTotalExcl()
    {
        return $this->totalExcl;
    }

    /**
     * Set delivery_cost
     *
     * @param decimal $deliveryCost
     */
    public function setDeliveryCost($deliveryCost)
    {
        $this->deliveryCost = $deliveryCost;
    }

    /**
     * Get delivery_cost
     *
     * @return decimal $deliveryCost
     */
    public function getDeliveryCost()
    {
        return $this->deliveryCost;
    }

    /**
     * Set billing_name
     *
     * @param string $billingName
     */
    public function setBillingName($billingName)
    {
        $this->billingName = $billingName;
    }

    /**
     * Get billing_name
     *
     * @return string $billingName
     */
    public function getBillingName()
    {
        return $this->billingName;
    }

    /**
     * Set billing_phone
     *
     * @param string $billingPhone
     */
    public function setBillingPhone($billingPhone)
    {
        $this->billingPhone = $billingPhone;
    }

    /**
     * Get billing_phone
     *
     * @return string $billingPhone
     */
    public function getBillingPhone()
    {
        return $this->billingPhone;
    }

    /**
     * Set billing_address1
     *
     * @param string $billingAddress1
     */
    public function setBillingAddress1($billingAddress1)
    {
        $this->billingAddress1 = $billingAddress1;
    }

    /**
     * Get billing_address1
     *
     * @return string $billingAddress1
     */
    public function getBillingAddress1()
    {
        return $this->billingAddress1;
    }

    /**
     * Set billing_address2
     *
     * @param string $billingAddress2
     */
    public function setBillingAddress2($billingAddress2)
    {
        $this->billingAddress2 = $billingAddress2;
    }

    /**
     * Get billing_address2
     *
     * @return string $billingAddress2
     */
    public function getBillingAddress2()
    {
        return $this->billingAddress2;
    }

    /**
     * Set billing_address3
     *
     * @param string $billingAddress3
     */
    public function setBillingAddress3($billingAddress3)
    {
        $this->billingAddress3 = $billingAddress3;
    }

    /**
     * Get billing_address3
     *
     * @return string $billingAddress3
     */
    public function getBillingAddress3()
    {
        return $this->billingAddress3;
    }

    /**
     * Set billing_city
     *
     * @param string $billingCity
     */
    public function setBillingCity($billingCity)
    {
        $this->billingCity = $billingCity;
    }

    /**
     * Get billing_city
     *
     * @return string $billingCity
     */
    public function getBillingCity()
    {
        return $this->billingCity;
    }

    /**
     * Set billing_postcode
     *
     * @param string $billingPostcode
     */
    public function setBillingPostcode($billingPostcode)
    {
        $this->billingPostcode = $billingPostcode;
    }

    /**
     * Get billing_postcode
     *
     * @return string $billingPostcode
     */
    public function getBillingPostcode()
    {
        return $this->billingPostcode;
    }

    /**
     * Set billing_country_code
     *
     * @param string $billingCountry
     */
    public function setBillingCountryCode($billingCountryCode)
    {
        $this->billingCountryCode = $billingCountryCode;
    }

    /**
     * Get billing_country
     *
     * @return string $billingCountryCode
     */
    public function getBillingCountryCode()
    {
        return $this->billingCountryCode;
    }

    /**
     * Set billing_fax
     *
     * @param string $billingFax
     */
    public function setBillingFax($billingFax)
    {
        $this->billingFax = $billingFax;
    }

    /**
     * Get billing_fax
     *
     * @return string $billingFax
     */
    public function getBillingFax()
    {
        return $this->billingFax;
    }

    /**
     * Set billing_email
     *
     * @param string $billingEmail
     */
    public function setBillingEmail($billingEmail)
    {
        $this->billingEmail = $billingEmail;
    }

    /**
     * Get billing_email
     *
     * @return string $billingEmail
     */
    public function getBillingEmail()
    {
        return $this->billingEmail;
    }

    /**
     * Set billing_mobile
     *
     * @param string $billingMobile
     */
    public function setBillingMobile($billingMobile)
    {
        $this->billingMobile = $billingMobile;
    }

    /**
     * Get billing_mobile
     *
     * @return string $billingMobile
     */
    public function getBillingMobile()
    {
        return $this->billingMobile;
    }

    /**
     * Set shipping_name
     *
     * @param string $shippingName
     */
    public function setShippingName($shippingName)
    {
        $this->shippingName = $shippingName;
    }

    /**
     * Get shipping_name
     *
     * @return string $shippingName
     */
    public function getShippingName()
    {
        return $this->shippingName;
    }

    /**
     * Set shipping_phone
     *
     * @param string $shippingPhone
     */
    public function setShippingPhone($shippingPhone)
    {
        $this->shippingPhone = $shippingPhone;
    }

    /**
     * Get shipping_phone
     *
     * @return string $shippingPhone
     */
    public function getShippingPhone()
    {
        return $this->shippingPhone;
    }

    /**
     * Set shipping_address1
     *
     * @param string $shippingAddress1
     */
    public function setShippingAddress1($shippingAddress1)
    {
        $this->shippingAddress1 = $shippingAddress1;
    }

    /**
     * Get shipping_address1
     *
     * @return string $shippingAddress1
     */
    public function getShippingAddress1()
    {
        return $this->shippingAddress1;
    }

    /**
     * Set shipping_address2
     *
     * @param string $shippingAddress2
     */
    public function setShippingAddress2($shippingAddress2)
    {
        $this->shippingAddress2 = $shippingAddress2;
    }

    /**
     * Get shipping_address2
     *
     * @return string $shippingAddress2
     */
    public function getShippingAddress2()
    {
        return $this->shippingAddress2;
    }

    /**
     * Set shipping_address3
     *
     * @param string $shippingAddress3
     */
    public function setShippingAddress3($shippingAddress3)
    {
        $this->shippingAddress3 = $shippingAddress3;
    }

    /**
     * Get shipping_address3
     *
     * @return string $shippingAddress3
     */
    public function getShippingAddress3()
    {
        return $this->shippingAddress3;
    }

    /**
     * Set shipping_city
     *
     * @param string $shippingCity
     */
    public function setShippingCity($shippingCity)
    {
        $this->shippingCity = $shippingCity;
    }

    /**
     * Get shipping_city
     *
     * @return string $shippingCity
     */
    public function getShippingCity()
    {
        return $this->shippingCity;
    }

    /**
     * Set shipping_postcode
     *
     * @param string $shippingPostcode
     */
    public function setShippingPostcode($shippingPostcode)
    {
        $this->shippingPostcode = $shippingPostcode;
    }

    /**
     * Get shipping_postcode
     *
     * @return string $shippingPostcode
     */
    public function getShippingPostcode()
    {
        return $this->shippingPostcode;
    }

    /**
     * Set shipping_country
     *
     * @param string $shippingCountry
     */
    public function setShippingCountryCode($shippingCountryCode)
    {
        $this->shippingCountryCode = $shippingCountryCode;
    }

    /**
     * Get shipping_country
     *
     * @return string $shippingCountry
     */
    public function getShippingCountryCode()
    {
        return $this->shippingCountryCode;
    }

    /**
     * Set shipping_fax
     *
     * @param string $shippingFax
     */
    public function setShippingFax($shippingFax)
    {
        $this->shippingFax = $shippingFax;
    }

    /**
     * Get shipping_fax
     *
     * @return string $shippingFax
     */
    public function getShippingFax()
    {
        return $this->shippingFax;
    }

    /**
     * Set shipping_email
     *
     * @param string $shippingEmail
     */
    public function setShippingEmail($shippingEmail)
    {
        $this->shippingEmail = $shippingEmail;
    }

    /**
     * Get shipping_email
     *
     * @return string $shippingEmail
     */
    public function getShippingEmail()
    {
        return $this->shippingEmail;
    }

    /**
     * Set shipping_mobile
     *
     * @param string $shippingMobile
     */
    public function setShippingMobile($shippingMobile)
    {
        $this->shippingMobile = $shippingMobile;
    }

    /**
     * Get shipping_mobile
     *
     * @return string $shippingMobile
     */
    public function getShippingMobile()
    {
        return $this->shippingMobile;
    }

    public function getOrderElements()
    {
        return $this->orderElements;
    }

    public function addOrderElement(OrderElementInterface $orderElement)
    {
        $this->orderElements[] = $orderElement;
        $orderElement->setOrder($this);
    }

    /**
     *
     * return true if the order is validated
     *
     * @return boolean
     */
    public function isValidated()
    {
        return $this->getValidatedAt() != null && $this->getStatus() == OrderInterface::STATUS_VALIDATED;
    }

    /**
     *
     *
     * @return boolean true if cancelled, else false
     */
    public function isCancelled()
    {
        return $this->getValidatedAt() != null && $this->getStatus() == OrderInterface::STATUS_CANCELLED;
    }

    /**
     *
     *
     * @return boolean true if pending, else false
     */
    public function isPending()
    {
        return in_array($this->getStatus(), array(OrderInterface::STATUS_PENDING));
    }

    /**
     * Return true if the order is open
     *
     * @return boolean
     */
    public function isOpen()
    {
        return in_array($this->getStatus(), array(OrderInterface::STATUS_OPEN));
    }

    /**
     * @return bool
     */
    public function isCancellable()
    {
        return $this->isOpen() || $this->isPending();
    }

    /**
     * Return true if the order has an error
     *
     * @return boolean
     */
    public function isError()
    {
        return in_array($this->getStatus(), array(OrderInterface::STATUS_ERROR));
    }

    /**
     * @param \DateTime|null $createdAt
     * @return void
     */
    public function setCreatedAt(\DateTime $createdAt = null)
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
     * @param \DateTime|null $updatedAt
     * @return void
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Add order_elements
     *
     * @param \Sonata\Component\Order\OrderElementInterface $orderElements
     */
    public function addOrderElements(OrderElementInterface $orderElements)
    {
        $this->orderElements[] = $orderElements;
    }

    /**
     * @param array $orderElements
     * @return void
     */
    public function setOrderElements($orderElements)
    {
        $this->orderElements = $orderElements;
    }

    /**
     * @param \Sonata\Component\Customer\CustomerInterface $customer
     * @return void
     */
    public function setCustomer(CustomerInterface $customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return \Sonata\Component\Customer\CustomerInterface
     */
    public function getCustomer()
    {
        return $this->customer;
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
     * @static
     * @return array
     */
    public static function getStatusList()
    {
        return array(
            self::STATUS_OPEN      => 'status_open',
            self::STATUS_PENDING   => 'status_pending',
            self::STATUS_VALIDATED => 'status_validated',
            self::STATUS_CANCELLED => 'status_cancelled',
            self::STATUS_ERROR     => 'status_error',
            self::STATUS_STOPPED   => 'status_stopped',
        );
    }
}