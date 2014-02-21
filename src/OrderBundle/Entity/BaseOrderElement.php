<?php

namespace Sonata\OrderBundle\Entity;

use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Order\OrderElementInterface;
use Sonata\Component\Product\ProductInterface;

use Application\Sonata\OrderBundle\Entity\Order;
use Application\Sonata\ProductBundle\Entity\Delivery;

/**
 * Sonata\OrderBundle\Entity\BaseOrderElement
 */
abstract class BaseOrderElement implements OrderElementInterface
{
    /**
     * @var integer $order
     */
    protected $order;

    /**
     * @var integer $quantity
     */
    protected $quantity;

    /**
     * @var float $price
     */
    protected $price;

    /**
     * @var float $unitPriceExcl
     */
    protected $unitPriceExcl;

    /**
     * @var float $unitPriceInc
     */
    protected $unitPriceInc;

    /**
     * @var float $vatRate
     */
    protected $vatRate;

    /**
     * @var string $designation
     */
    protected $designation;

    /**
     * @var string $description
     */
    protected $description;

    /**
     * @var array $options
     */
    protected $options;

    /**
     * @var array $options
     */
    protected $rawProduct;

    /**
     * @var integer $productId
     */
    protected $productId;

    /**
     * @var integer $status
     */
    protected $status;

    /**
     * @var integer $delivery_status
     */
    protected $deliveryStatus;

    /**
     * @var \DateTime $validated_at
     */
    protected $validatedAt;

    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var string $product_type
     */
    protected $productType;

    protected $createdAt;

    protected $updatedAt;

    public function __construct()
    {
        $this->rawProduct = array();
        $this->options = array();
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
     * Set order
     *
     * @param OrderInterface $order
     */
    public function setOrder(OrderInterface $order)
    {
        $this->order = $order;
    }

    /**
     * Get order
     *
     * @return Order $order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * {@inheritdoc}
     */
    public function setQuantity($quantity)
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
    public function setPrice($price)
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
    public function setVatRate($vatRate)
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
     * Returns VAT element amount
     *
     * @return float
     */
    public function getVatAmount()
    {
        return bcsub($this->getTotal(true), $this->getTotal(false));
    }

    /**
     * Set designation
     *
     * @param string $designation
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;
    }

    /**
     * Get designation
     *
     * @return string $designation
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set productId
     *
     * @param integer $productId
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    /**
     * Get productId
     *
     * @return integer $productId
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set status
     *
     * @param integer $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
        if ($this->getStatus() == OrderInterface::STATUS_VALIDATED) {
            $this->setValidatedAt(new \DateTime);
        }
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
     * @return boolean true if cancelled, else false
     */
    public function isCancelled()
    {
        return $this->getValidatedAt() != null && $this->getStatus() == OrderInterface::STATUS_CANCELLED;
    }

    /**
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
     * @return \DateTime $validatedAt
     */
    public function getValidatedAt()
    {
        return $this->validatedAt;
    }

    /**
     * Add product
     *
     * @param ProductInterface $product
     */
    public function setProduct(ProductInterface $product)
    {
        $this->product = $product;
    }

    /**
     * Get product
     *
     * @return ProductInterface $product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set product_type
     *
     * @param string $productType
     */
    public function setProductType($productType)
    {
        $this->productType = $productType;
    }

    /**
     * Get product_type
     *
     * @return string $productType
     */
    public function getProductType()
    {
        return $this->productType;
    }

    /**
     * @param \DateTime|null $createdAt
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
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @return array
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
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * @param array $rawProduct
     */
    public function setRawProduct($rawProduct)
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
     * @return array
     */
    public function getRawProduct()
    {
        return $this->rawProduct;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getDesignation() ?: 'n/a';
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
        return Order::getStatusList();
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
     * @return array
     */
    public static function getDeliveryStatusList()
    {
        return Delivery::getStatusList();
    }

    /**
     * Sets unit price excluding VAT
     *
     * @param float $unitPriceExcl
     */
    public function setUnitPriceExcl($unitPriceExcl)
    {
        $this->unitPriceExcl = $unitPriceExcl;
    }

    /**
     * Returns unit price including VAT
     *
     * @return float
     */
    public function getUnitPriceExcl()
    {
        return $this->unitPriceExcl;
    }

    /**
     * Sets unit price including VAT
     *
     * @param float $unitPriceInc
     */
    public function setUnitPriceInc($unitPriceInc)
    {
        $this->unitPriceInc = $unitPriceInc;
    }

    /**
     * Returns unit price including VAT
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
     * Return the total (price * quantity)
     *
     * if $vat = true, return the price with vat
     *
     * @param boolean $vat
     *
     * @return float
     */
    public function getTotal($vat = false)
    {
        return bcmul($this->getUnitPrice($vat), $this->getQuantity());
    }

    /**
     * Returns the total with vat due to limitation of AdminBundle
     *
     * @return float
     */
    public function getTotalWithVat()
    {
        return $this->getTotal(true);
    }
}
