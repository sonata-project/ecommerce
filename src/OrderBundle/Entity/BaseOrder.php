<?php

namespace Sonata\OrderBundle\Entity;

use Sonata\Component\Delivery\BaseServiceDelivery;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Order\OrderElementInterface;
use Sonata\Component\Customer\CustomerInterface;
use Sonata\Component\Currency\CurrencyInterface;

use Sonata\CustomerBundle\Entity\BaseAddress;
use Sonata\PaymentBundle\Entity\BaseTransaction;

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
     * @var CurrencyInterface $currency
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
     * @var float $totalInc
     */
    protected $totalInc;

    /**
     * @var float $totalExcl
     */
    protected $totalExcl;

    /**
     * @var float $delivery_cost
     */
    protected $deliveryCost;

    /**
     * @var float $deliverVat
     */
    protected $deliveryVat;

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

    protected $locale;

    public function __construct()
    {
        $this->orderElements     = new \Doctrine\Common\Collections\ArrayCollection;
    }

    public function prePersist()
    {
        $this->setCreatedAt(new \DateTime);
        $this->setUpdatedAt(new \DateTime);
    }

    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getReference() ?: 'n/a';
    }

    /**
     * Returns formatted delivery address
     *
     * @param string $sep
     *
     * @return string
     */
    public function getFullDelivery($sep = ", ")
    {
        return BaseAddress::formatAddress($this->getDeliveryAsArray(), $sep);
    }

    /**
     * @return array
     */
    public function getDeliveryAsArray()
    {
        return array(
            'firstname'    => $this->getShippingName(),
            'lastname'     => "",
            'address1'     => $this->getShippingAddress1(),
            'postcode'     => $this->getShippingPostcode(),
            'city'         => $this->getShippingCity(),
            'country_code' => $this->getShippingCountryCode()
        );
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
        return BaseAddress::formatAddress($this->getBillingAsArray(),$sep);
    }

    public function getBillingAsArray()
    {
        return array(
            'firstname'    => $this->getBillingName(),
            'lastname'     => "",
            'address1'     => $this->getBillingAddress1(),
            'postcode'     => $this->getBillingPostcode(),
            'city'         => $this->getBillingCity(),
            'country_code' => $this->getBillingCountryCode()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setReference($reference)
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
     * {@inheritdoc}
     */
    public function setDeliveryMethod($deliveryMethod)
    {
        $this->deliveryMethod = $deliveryMethod;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeliveryMethod()
    {
        return $this->deliveryMethod;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency(CurrencyInterface $currency)
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
    public function setStatus($status)
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
    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;
    }

    /**
     * {@inheritdoc}
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
        $statusList = BaseTransaction::getStatusList();

        return $statusList[$this->getPaymentStatus()];
    }

    /**
     * {@inheritdoc}
     */
    public function setDeliveryStatus($deliveryStatus)
    {
        $this->deliveryStatus = $deliveryStatus;
    }

    /**
     * {@inheritdoc}
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
        $statusList = BaseServiceDelivery::getStatusList();

        return $statusList[$this->getDeliveryStatus()];
    }

    /**
     * {@inheritdoc}
     */
    public function setValidatedAt(\DateTime $validatedAt = null)
    {
        $this->validatedAt = $validatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getValidatedAt()
    {
        return $this->validatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalInc($totalInc)
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
    public function setTotalExcl($totalExcl)
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
     * {@inheritdoc}
     */
    public function setDeliveryCost($deliveryCost)
    {
        $this->deliveryCost = $deliveryCost;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeliveryCost()
    {
        return $this->deliveryCost;
    }

    /**
     * Set delivery VAT
     *
     * @param float $deliveryVat
     */
    public function setDeliveryVat($deliveryVat)
    {
        $this->deliveryVat = $deliveryVat;
    }

    /**
     * Get delivery VAT
     *
     * @return float $deliveryVat
     */
    public function getDeliveryVat()
    {
        return $this->deliveryVat;
    }


    /**
     * {@inheritdoc}
     */
    public function setBillingName($billingName)
    {
        $this->billingName = $billingName;
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingName()
    {
        return $this->billingName;
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingPhone($billingPhone)
    {
        $this->billingPhone = $billingPhone;
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingPhone()
    {
        return $this->billingPhone;
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingAddress1($billingAddress1)
    {
        $this->billingAddress1 = $billingAddress1;
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAddress1()
    {
        return $this->billingAddress1;
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingAddress2($billingAddress2)
    {
        $this->billingAddress2 = $billingAddress2;
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAddress2()
    {
        return $this->billingAddress2;
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingAddress3($billingAddress3)
    {
        $this->billingAddress3 = $billingAddress3;
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAddress3()
    {
        return $this->billingAddress3;
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingCity($billingCity)
    {
        $this->billingCity = $billingCity;
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingCity()
    {
        return $this->billingCity;
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingPostcode($billingPostcode)
    {
        $this->billingPostcode = $billingPostcode;
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingPostcode()
    {
        return $this->billingPostcode;
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingCountryCode($billingCountryCode)
    {
        $this->billingCountryCode = $billingCountryCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingCountryCode()
    {
        return $this->billingCountryCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingFax($billingFax)
    {
        $this->billingFax = $billingFax;
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingFax()
    {
        return $this->billingFax;
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingEmail($billingEmail)
    {
        $this->billingEmail = $billingEmail;
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingEmail()
    {
        return $this->billingEmail;
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingMobile($billingMobile)
    {
        $this->billingMobile = $billingMobile;
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingMobile()
    {
        return $this->billingMobile;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingName($shippingName)
    {
        $this->shippingName = $shippingName;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingName()
    {
        return $this->shippingName;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingPhone($shippingPhone)
    {
        $this->shippingPhone = $shippingPhone;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingPhone()
    {
        return $this->shippingPhone;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingAddress1($shippingAddress1)
    {
        $this->shippingAddress1 = $shippingAddress1;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingAddress1()
    {
        return $this->shippingAddress1;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingAddress2($shippingAddress2)
    {
        $this->shippingAddress2 = $shippingAddress2;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingAddress2()
    {
        return $this->shippingAddress2;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingAddress3($shippingAddress3)
    {
        $this->shippingAddress3 = $shippingAddress3;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingAddress3()
    {
        return $this->shippingAddress3;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingCity($shippingCity)
    {
        $this->shippingCity = $shippingCity;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingCity()
    {
        return $this->shippingCity;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingPostcode($shippingPostcode)
    {
        $this->shippingPostcode = $shippingPostcode;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingPostcode()
    {
        return $this->shippingPostcode;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingCountryCode($shippingCountryCode)
    {
        $this->shippingCountryCode = $shippingCountryCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingCountryCode()
    {
        return $this->shippingCountryCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingFax($shippingFax)
    {
        $this->shippingFax = $shippingFax;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingFax()
    {
        return $this->shippingFax;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingEmail($shippingEmail)
    {
        $this->shippingEmail = $shippingEmail;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingEmail()
    {
        return $this->shippingEmail;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingMobile($shippingMobile)
    {
        $this->shippingMobile = $shippingMobile;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingMobile()
    {
        return $this->shippingMobile;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderElements()
    {
        return $this->orderElements;
    }

    /**
     * {@inheritdoc}
     */
    public function addOrderElement(OrderElementInterface $orderElement)
    {
        $this->orderElements[] = $orderElement;
        $orderElement->setOrder($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isValidated()
    {
        return $this->getValidatedAt() != null && $this->getStatus() == OrderInterface::STATUS_VALIDATED;
    }

    /**
     * {@inheritdoc}
     */
    public function isCancelled()
    {
        return $this->getValidatedAt() != null && $this->getStatus() == OrderInterface::STATUS_CANCELLED;
    }

    /**
     * {@inheritdoc}
     */
    public function isPending()
    {
        return in_array($this->getStatus(), array(OrderInterface::STATUS_PENDING));
    }

    /**
     * {@inheritdoc}
     */
    public function isOpen()
    {
        return in_array($this->getStatus(), array(OrderInterface::STATUS_OPEN));
    }

    /**
     * {@inheritdoc}
     */
    public function isCancellable()
    {
        return $this->isOpen() || $this->isPending();
    }

    /**
     * {@inheritdoc}
     */
    public function isError()
    {
        return in_array($this->getStatus(), array(OrderInterface::STATUS_ERROR));
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt = null)
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
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function addOrderElements(OrderElementInterface $orderElements)
    {
        $this->orderElements[] = $orderElements;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderElements($orderElements)
    {
        $this->orderElements = $orderElements;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomer(CustomerInterface $customer)
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

    /**
     * @return array
     */
    public static function getValidationStatusList()
    {
        return array_keys(self::getStatusList());
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
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
    public function getVat()
    {
        return bcsub($this->totalInc, $this->totalExcl);
    }

    /**
     * Returns all VAT amounts contained in elements
     *
     * @return array
     */
    public function getVatAmounts()
    {
        $amounts = array();

        foreach ($this->getOrderElements() as $orderElement) {
            $rate = $orderElement->getVatRate();

            if (isset($amounts[$rate])) {
                $amounts[$rate]['amount'] = bcadd($amounts[$rate]['amount'], $orderElement->getVatAmount());
            } else {
                $amounts[$rate] = array(
                    'rate' => $rate,
                    'amount' => $orderElement->getVatAmount()
                );
            }
        }

        return $amounts;
    }
}
