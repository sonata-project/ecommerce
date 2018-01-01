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
     * @var \DateTime
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

    /**
     * @var ArrayCollection|OrderElementInterface[]
     */
    protected $orderElements;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var CustomerInterface
     */
    protected $customer;

    /**
     * @var string
     */
    protected $locale;

    public function __construct()
    {
        $this->orderElements = new ArrayCollection();
    }

    /**
     * @return string
     */
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

    /**
     * @return array
     */
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
    public function setDeliveryMethod($deliveryMethod): void
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
    public function setPaymentStatus($paymentStatus): void
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
    public function setDeliveryStatus($deliveryStatus): void
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
    public function setValidatedAt(\DateTime $validatedAt = null): void
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
    public function setUsername($username): void
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
     * {@inheritdoc}
     */
    public function setDeliveryCost($deliveryCost): void
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
     * {@inheritdoc}
     */
    public function setDeliveryVat($deliveryVat): void
    {
        $this->deliveryVat = $deliveryVat;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeliveryVat()
    {
        return $this->deliveryVat;
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingName($billingName): void
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
    public function setBillingPhone($billingPhone): void
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
    public function setBillingAddress1($billingAddress1): void
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
    public function setBillingAddress2($billingAddress2): void
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
    public function setBillingAddress3($billingAddress3): void
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
    public function setBillingCity($billingCity): void
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
    public function setBillingPostcode($billingPostcode): void
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
    public function setBillingCountryCode($billingCountryCode): void
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
    public function setBillingFax($billingFax): void
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
    public function setBillingEmail($billingEmail): void
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
    public function setBillingMobile($billingMobile): void
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
    public function setShippingName($shippingName): void
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
    public function setShippingPhone($shippingPhone): void
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
    public function setShippingAddress1($shippingAddress1): void
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
    public function setShippingAddress2($shippingAddress2): void
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
    public function setShippingAddress3($shippingAddress3): void
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
    public function setShippingCity($shippingCity): void
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
    public function setShippingPostcode($shippingPostcode): void
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
    public function setShippingCountryCode($shippingCountryCode): void
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
    public function setShippingFax($shippingFax): void
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
    public function setShippingEmail($shippingEmail): void
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
    public function setShippingMobile($shippingMobile): void
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
    public function addOrderElement(OrderElementInterface $orderElement): void
    {
        $this->orderElements[] = $orderElement;
        $orderElement->setOrder($this);
    }

    /**
     * {@inheritdoc}
     */
    public function isValidated()
    {
        return null != $this->getValidatedAt() && OrderInterface::STATUS_VALIDATED == $this->getStatus();
    }

    /**
     * {@inheritdoc}
     */
    public function isCancelled()
    {
        return null != $this->getValidatedAt() && OrderInterface::STATUS_CANCELLED == $this->getStatus();
    }

    /**
     * {@inheritdoc}
     */
    public function isPending()
    {
        return in_array($this->getStatus(), [OrderInterface::STATUS_PENDING]);
    }

    /**
     * {@inheritdoc}
     */
    public function isOpen()
    {
        return in_array($this->getStatus(), [OrderInterface::STATUS_OPEN]);
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
        return in_array($this->getStatus(), [OrderInterface::STATUS_ERROR]);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt = null): void
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
    public function setUpdatedAt(\DateTime $updatedAt = null): void
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
    public function addOrderElements(OrderElementInterface $orderElements): void
    {
        $this->orderElements[] = $orderElements;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderElements($orderElements): void
    {
        $this->orderElements = $orderElements;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomer(CustomerInterface $customer): void
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

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale): void
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
