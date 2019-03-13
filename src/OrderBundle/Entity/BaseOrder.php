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

namespace Sonata\OrderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Sonata\Component\Currency\CurrencyInterface;
use Sonata\Component\Customer\CustomerInterface;
use Sonata\Component\Delivery\BaseServiceDelivery;
use Sonata\Component\Order\OrderElementInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\CustomerBundle\Entity\BaseAddress;
use Sonata\PaymentBundle\Entity\BaseTransaction;

abstract class BaseOrder implements OrderInterface
{
    /**
     * @var string
     */
    protected $reference;

    /**
     * @var string
     */
    protected $paymentMethod;

    /**
     * @var string
     */
    protected $deliveryMethod;

    /**
     * @var CurrencyInterface
     */
    protected $currency;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var int
     */
    protected $paymentStatus;

    /**
     * @var int
     */
    protected $deliveryStatus;

    /**
     * @var datetime
     */
    protected $validatedAt;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var float
     */
    protected $totalInc;

    /**
     * @var float
     */
    protected $totalExcl;

    /**
     * @var float
     */
    protected $deliveryCost;

    /**
     * @var float
     */
    protected $deliveryVat;

    /**
     * @var string
     */
    protected $billingName;

    /**
     * @var string
     */
    protected $billingPhone;

    /**
     * @var string
     */
    protected $billingAddress1;

    /**
     * @var string
     */
    protected $billingAddress2;

    /**
     * @var string
     */
    protected $billingAddress3;

    /**
     * @var string
     */
    protected $billingCity;

    /**
     * @var string
     */
    protected $billingPostcode;

    /**
     * @var string
     */
    protected $billingCountryCode;

    /**
     * @var string
     */
    protected $billingFax;

    /**
     * @var string
     */
    protected $billingEmail;

    /**
     * @var string
     */
    protected $billingMobile;

    /**
     * @var string
     */
    protected $shippingName;

    /**
     * @var string
     */
    protected $shippingPhone;

    /**
     * @var string
     */
    protected $shippingAddress1;

    /**
     * @var string
     */
    protected $shippingAddress2;

    /**
     * @var string
     */
    protected $shippingAddress3;

    /**
     * @var string
     */
    protected $shippingCity;

    /**
     * @var string
     */
    protected $shippingPostcode;

    /**
     * @var string
     */
    protected $shippingCountryCode;

    /**
     * @var string
     */
    protected $shippingFax;

    /**
     * @var string
     */
    protected $shippingEmail;

    /**
     * @var string
     */
    protected $shippingMobile;

    protected $orderElements;

    protected $createdAt;

    protected $updatedAt;

    protected $customer;

    protected $locale;

    public function __construct()
    {
        $this->orderElements = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getReference() ?: 'n/a';
    }

    public function prePersist(): void
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    public function preUpdate(): void
    {
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * Returns formatted delivery address.
     *
     * @param string $sep
     *
     * @return string
     */
    public function getFullDelivery($sep = ', ')
    {
        return BaseAddress::formatAddress($this->getDeliveryAsArray(), $sep);
    }

    /**
     * @return array
     */
    public function getDeliveryAsArray()
    {
        return [
            'firstname' => $this->getShippingName(),
            'lastname' => '',
            'address1' => $this->getShippingAddress1(),
            'postcode' => $this->getShippingPostcode(),
            'city' => $this->getShippingCity(),
            'country_code' => $this->getShippingCountryCode(),
        ];
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

    public function getBillingAsArray()
    {
        return [
            'firstname' => $this->getBillingName(),
            'lastname' => '',
            'address1' => $this->getBillingAddress1(),
            'postcode' => $this->getBillingPostcode(),
            'city' => $this->getBillingCity(),
            'country_code' => $this->getBillingCountryCode(),
        ];
    }

    public function setReference($reference): void
    {
        $this->reference = $reference;
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function setPaymentMethod($paymentMethod): void
    {
        $this->paymentMethod = $paymentMethod;
    }

    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    public function setDeliveryMethod($deliveryMethod): void
    {
        $this->deliveryMethod = $deliveryMethod;
    }

    public function getDeliveryMethod()
    {
        return $this->deliveryMethod;
    }

    public function setCurrency(CurrencyInterface $currency): void
    {
        $this->currency = $currency;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setStatus($status): void
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setPaymentStatus($paymentStatus): void
    {
        $this->paymentStatus = $paymentStatus;
    }

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

    public function setDeliveryStatus($deliveryStatus): void
    {
        $this->deliveryStatus = $deliveryStatus;
    }

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

    public function setValidatedAt(\DateTime $validatedAt = null): void
    {
        $this->validatedAt = $validatedAt;
    }

    public function getValidatedAt()
    {
        return $this->validatedAt;
    }

    public function setUsername($username): void
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setTotalInc($totalInc): void
    {
        $this->totalInc = $totalInc;
    }

    public function getTotalInc()
    {
        return $this->totalInc;
    }

    public function setTotalExcl($totalExcl): void
    {
        $this->totalExcl = $totalExcl;
    }

    public function getTotalExcl()
    {
        return $this->totalExcl;
    }

    public function setDeliveryCost($deliveryCost): void
    {
        $this->deliveryCost = $deliveryCost;
    }

    public function getDeliveryCost()
    {
        return $this->deliveryCost;
    }

    /**
     * Set delivery VAT.
     *
     * @param float $deliveryVat
     */
    public function setDeliveryVat($deliveryVat): void
    {
        $this->deliveryVat = $deliveryVat;
    }

    /**
     * Get delivery VAT.
     *
     * @return float $deliveryVat
     */
    public function getDeliveryVat()
    {
        return $this->deliveryVat;
    }

    public function setBillingName($billingName): void
    {
        $this->billingName = $billingName;
    }

    public function getBillingName()
    {
        return $this->billingName;
    }

    public function setBillingPhone($billingPhone): void
    {
        $this->billingPhone = $billingPhone;
    }

    public function getBillingPhone()
    {
        return $this->billingPhone;
    }

    public function setBillingAddress1($billingAddress1): void
    {
        $this->billingAddress1 = $billingAddress1;
    }

    public function getBillingAddress1()
    {
        return $this->billingAddress1;
    }

    public function setBillingAddress2($billingAddress2): void
    {
        $this->billingAddress2 = $billingAddress2;
    }

    public function getBillingAddress2()
    {
        return $this->billingAddress2;
    }

    public function setBillingAddress3($billingAddress3): void
    {
        $this->billingAddress3 = $billingAddress3;
    }

    public function getBillingAddress3()
    {
        return $this->billingAddress3;
    }

    public function setBillingCity($billingCity): void
    {
        $this->billingCity = $billingCity;
    }

    public function getBillingCity()
    {
        return $this->billingCity;
    }

    public function setBillingPostcode($billingPostcode): void
    {
        $this->billingPostcode = $billingPostcode;
    }

    public function getBillingPostcode()
    {
        return $this->billingPostcode;
    }

    public function setBillingCountryCode($billingCountryCode): void
    {
        $this->billingCountryCode = $billingCountryCode;
    }

    public function getBillingCountryCode()
    {
        return $this->billingCountryCode;
    }

    public function setBillingFax($billingFax): void
    {
        $this->billingFax = $billingFax;
    }

    public function getBillingFax()
    {
        return $this->billingFax;
    }

    public function setBillingEmail($billingEmail): void
    {
        $this->billingEmail = $billingEmail;
    }

    public function getBillingEmail()
    {
        return $this->billingEmail;
    }

    public function setBillingMobile($billingMobile): void
    {
        $this->billingMobile = $billingMobile;
    }

    public function getBillingMobile()
    {
        return $this->billingMobile;
    }

    public function setShippingName($shippingName): void
    {
        $this->shippingName = $shippingName;
    }

    public function getShippingName()
    {
        return $this->shippingName;
    }

    public function setShippingPhone($shippingPhone): void
    {
        $this->shippingPhone = $shippingPhone;
    }

    public function getShippingPhone()
    {
        return $this->shippingPhone;
    }

    public function setShippingAddress1($shippingAddress1): void
    {
        $this->shippingAddress1 = $shippingAddress1;
    }

    public function getShippingAddress1()
    {
        return $this->shippingAddress1;
    }

    public function setShippingAddress2($shippingAddress2): void
    {
        $this->shippingAddress2 = $shippingAddress2;
    }

    public function getShippingAddress2()
    {
        return $this->shippingAddress2;
    }

    public function setShippingAddress3($shippingAddress3): void
    {
        $this->shippingAddress3 = $shippingAddress3;
    }

    public function getShippingAddress3()
    {
        return $this->shippingAddress3;
    }

    public function setShippingCity($shippingCity): void
    {
        $this->shippingCity = $shippingCity;
    }

    public function getShippingCity()
    {
        return $this->shippingCity;
    }

    public function setShippingPostcode($shippingPostcode): void
    {
        $this->shippingPostcode = $shippingPostcode;
    }

    public function getShippingPostcode()
    {
        return $this->shippingPostcode;
    }

    public function setShippingCountryCode($shippingCountryCode): void
    {
        $this->shippingCountryCode = $shippingCountryCode;
    }

    public function getShippingCountryCode()
    {
        return $this->shippingCountryCode;
    }

    public function setShippingFax($shippingFax): void
    {
        $this->shippingFax = $shippingFax;
    }

    public function getShippingFax()
    {
        return $this->shippingFax;
    }

    public function setShippingEmail($shippingEmail): void
    {
        $this->shippingEmail = $shippingEmail;
    }

    public function getShippingEmail()
    {
        return $this->shippingEmail;
    }

    public function setShippingMobile($shippingMobile): void
    {
        $this->shippingMobile = $shippingMobile;
    }

    public function getShippingMobile()
    {
        return $this->shippingMobile;
    }

    public function getOrderElements()
    {
        return $this->orderElements;
    }

    public function addOrderElement(OrderElementInterface $orderElement): void
    {
        $this->orderElements[] = $orderElement;
        $orderElement->setOrder($this);
    }

    public function isValidated()
    {
        return null !== $this->getValidatedAt() && OrderInterface::STATUS_VALIDATED === $this->getStatus();
    }

    public function isCancelled()
    {
        return null !== $this->getValidatedAt() && OrderInterface::STATUS_CANCELLED === $this->getStatus();
    }

    public function isPending()
    {
        return \in_array($this->getStatus(), [OrderInterface::STATUS_PENDING], true);
    }

    public function isOpen()
    {
        return \in_array($this->getStatus(), [OrderInterface::STATUS_OPEN], true);
    }

    public function isCancellable()
    {
        return $this->isOpen() || $this->isPending();
    }

    public function isError()
    {
        return \in_array($this->getStatus(), [OrderInterface::STATUS_ERROR], true);
    }

    public function setCreatedAt(\DateTime $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt = null): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function addOrderElements(OrderElementInterface $orderElements): void
    {
        $this->orderElements[] = $orderElements;
    }

    public function setOrderElements($orderElements): void
    {
        $this->orderElements = $orderElements;
    }

    public function setCustomer(CustomerInterface $customer): void
    {
        $this->customer = $customer;
    }

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
     *
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_OPEN => 'status_open',
            self::STATUS_PENDING => 'status_pending',
            self::STATUS_VALIDATED => 'status_validated',
            self::STATUS_CANCELLED => 'status_cancelled',
            self::STATUS_ERROR => 'status_error',
            self::STATUS_STOPPED => 'status_stopped',
        ];
    }

    /**
     * @return array
     */
    public static function getValidationStatusList()
    {
        return array_keys(self::getStatusList());
    }

    public function setLocale($locale): void
    {
        $this->locale = $locale;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function getVat()
    {
        return bcsub($this->totalInc, $this->totalExcl);
    }

    /**
     * Returns all VAT amounts contained in elements.
     *
     * @return array
     */
    public function getVatAmounts()
    {
        $amounts = [];

        foreach ($this->getOrderElements() as $orderElement) {
            $rate = $orderElement->getVatRate();
            $amount = (string) $orderElement->getVatAmount();

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
}
