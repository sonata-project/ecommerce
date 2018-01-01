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

use Sonata\Component\Order\OrderElementInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\ProductBundle\Entity\BaseDelivery;

abstract class BaseOrderElement implements OrderElementInterface
{
    /**
     * @var OrderInterface
     */
    protected $order;

    /**
     * @var int
     */
    protected $quantity;

    /**
     * @var float
     */
    protected $price;

    /**
     * @var float
     */
    protected $unitPriceExcl;

    /**
     * @var float
     */
    protected $unitPriceInc;

    /**
     * @var float
     */
    protected $vatRate;

    /**
     * @var string
     */
    protected $designation;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var array
     */
    protected $rawProduct;

    /**
     * @var int
     */
    protected $productId;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var int
     */
    protected $deliveryStatus;

    /**
     * @var \DateTime
     */
    protected $validatedAt;

    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var string
     */
    protected $productType;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->rawProduct = [];
        $this->options = [];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getDesignation() ?: 'n/a';
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
     * @param OrderInterface $order
     */
    public function setOrder(OrderInterface $order): void
    {
        $this->order = $order;
    }

    /**
     * @return OrderInterface
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * {@inheritdoc}
     */
    public function setQuantity($quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice($vat = false)
    {
        $unitPrice = $this->getUnitPriceExcl();

        if ($vat) {
            $unitPrice = $this->getUnitPriceInc();
        }

        return bcmul($unitPrice, $this->getQuantity());
    }

    /**
     * {@inheritdoc}
     */
    public function setVatRate($vatRate): void
    {
        $this->vatRate = $vatRate;
    }

    /**
     * {@inheritdoc}
     */
    public function getVatRate()
    {
        return $this->vatRate;
    }

    /**
     * Returns VAT element amount.
     *
     * @return float
     */
    public function getVatAmount()
    {
        return bcsub($this->getTotal(true), $this->getTotal(false));
    }

    /**
     * {@inheritdoc}
     */
    public function setDesignation($designation): void
    {
        $this->designation = $designation;
    }

    /**
     * {@inheritdoc}
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param int $productId
     */
    public function setProductId($productId): void
    {
        $this->productId = $productId;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status): void
    {
        $this->status = $status;
        if (OrderInterface::STATUS_VALIDATED == $this->getStatus()) {
            $this->setValidatedAt(new \DateTime());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * return true if the order is validated.
     *
     * @return bool
     */
    public function isValidated()
    {
        return null != $this->getValidatedAt() && OrderInterface::STATUS_VALIDATED == $this->getStatus();
    }

    /**
     * @return bool true if cancelled, else false
     */
    public function isCancelled()
    {
        return null != $this->getValidatedAt() && OrderInterface::STATUS_CANCELLED == $this->getStatus();
    }

    /**
     * @return bool true if pending, else false
     */
    public function isPending()
    {
        return in_array($this->getStatus(), [OrderInterface::STATUS_PENDING]);
    }

    /**
     * Return true if the order is open.
     *
     * @return bool
     */
    public function isOpen()
    {
        return in_array($this->getStatus(), [OrderInterface::STATUS_OPEN]);
    }

    /**
     * @return bool
     */
    public function isCancellable()
    {
        return $this->isOpen() || $this->isPending();
    }

    /**
     * Return true if the order has an error.
     *
     * @return bool
     */
    public function isError()
    {
        return in_array($this->getStatus(), [OrderInterface::STATUS_ERROR]);
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
    public function setProduct(ProductInterface $product): void
    {
        $this->product = $product;
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * {@inheritdoc}
     */
    public function setProductType($productType): void
    {
        $this->productType = $productType;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductType()
    {
        return $this->productType;
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
    public function setOptions($options): void
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getOption($name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasOption($name)
    {
        return isset($this->options[$name]);
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setOption($name, $value): void
    {
        $this->options[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function setRawProduct($rawProduct): void
    {
        $this->rawProduct = $rawProduct;
    }

    /**
     * @param string $name
     * @param string $default
     *
     * @return mixed
     */
    public function getRawProductValue($name, $default = null)
    {
        $values = $this->getRawProduct();

        if (array_key_exists($name, $values)) {
            return $values[$name];
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getRawProduct()
    {
        return $this->rawProduct;
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
        return BaseOrder::getStatusList();
    }

    /**
     * @return string
     */
    public function getDeliveryStatusName()
    {
        $statusList = self::getDeliveryStatusList();

        return $statusList[$this->deliveryStatus];
    }

    /**
     * @static
     *
     * @return array
     */
    public static function getDeliveryStatusList()
    {
        return BaseDelivery::getStatusList();
    }

    /**
     * Sets unit price excluding VAT.
     *
     * @param float $unitPriceExcl
     */
    public function setUnitPriceExcl($unitPriceExcl): void
    {
        $this->unitPriceExcl = $unitPriceExcl;
    }

    /**
     * Returns unit price including VAT.
     *
     * @return float
     */
    public function getUnitPriceExcl()
    {
        return $this->unitPriceExcl;
    }

    /**
     * Sets unit price including VAT.
     *
     * @param float $unitPriceInc
     */
    public function setUnitPriceInc($unitPriceInc): void
    {
        $this->unitPriceInc = $unitPriceInc;
    }

    /**
     * Returns unit price including VAT.
     *
     * @return float
     */
    public function getUnitPriceInc()
    {
        return $this->unitPriceInc;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnitPrice($vat = false)
    {
        return $vat ? $this->getUnitPriceInc() : $this->getUnitPriceExcl();
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal($vat = false)
    {
        return bcmul($this->getUnitPrice($vat), $this->getQuantity());
    }

    /**
     * Returns the total with vat due to limitation of AdminBundle.
     *
     * @return float
     */
    public function getTotalWithVat()
    {
        return $this->getTotal(true);
    }
}
