<?php

namespace Sonata\Bundle\OrderBundle\Entity;

/**
 * Sonata\Bundle\OrderBundle\Entity\BaseOrderElement
 */
abstract class BaseOrderElement
{
    /**
     * @var integer $order_id
     */
    protected $order_id;

    /**
     * @var integer $quantity
     */
    protected $quantity;

    /**
     * @var decimal $price
     */
    protected $price;

    /**
     * @var decimal $vat
     */
    protected $vat;

    /**
     * @var string $designation
     */
    protected $designation;

    /**
     * @var text $description
     */
    protected $description;

    /**
     * @var text $serialize
     */
    protected $serialize;

    /**
     * @var integer $type
     */
    protected $type;

    /**
     * @var integer $product_id
     */
    protected $product_id;

    /**
     * @var integer $status
     */
    protected $status;

    /**
     * @var integer $delivery_status
     */
    protected $delivery_status;

    /**
     * @var string $behavior_class
     */
    protected $behavior_class;

    /**
     * @var datetime $validated_at
     */
    protected $validated_at;

    /**
     * @var Sonata\Bundle\ProductBundle\Entity\BaseProduct
     */
    protected $product;

    /**
     * Set order_id
     *
     * @param integer $orderId
     */
    public function setOrderId($orderId)
    {
        $this->order_id = $orderId;
    }

    /**
     * Get order_id
     *
     * @return integer $orderId
     */
    public function getOrderId()
    {
        return $this->order_id;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * Get quantity
     *
     * @return integer $quantity
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set price
     *
     * @param decimal $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * Get price
     *
     * @return decimal $price
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set vat
     *
     * @param decimal $vat
     */
    public function setVat($vat)
    {
        $this->vat = $vat;
    }

    /**
     * Get vat
     *
     * @return decimal $vat
     */
    public function getVat()
    {
        return $this->vat;
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
     * @param text $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return text $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set serialize
     *
     * @param text $serialize
     */
    public function setSerialize($serialize)
    {
        $this->serialize = $serialize;
    }

    /**
     * Get serialize
     *
     * @return text $serialize
     */
    public function getSerialize()
    {
        return $this->serialize;
    }

    /**
     * Set type
     *
     * @param integer $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return integer $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set product_id
     *
     * @param integer $productId
     */
    public function setProductId($productId)
    {
        $this->product_id = $productId;
    }

    /**
     * Get product_id
     *
     * @return integer $productId
     */
    public function getProductId()
    {
        return $this->product_id;
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
     * Set delivery_status
     *
     * @param integer $deliveryStatus
     */
    public function setDeliveryStatus($deliveryStatus)
    {
        $this->delivery_status = $deliveryStatus;
    }

    /**
     * Get delivery_status
     *
     * @return integer $deliveryStatus
     */
    public function getDeliveryStatus()
    {
        return $this->delivery_status;
    }

    /**
     * Set behavior_class
     *
     * @param string $behaviorClass
     */
    public function setBehaviorClass($behaviorClass)
    {
        $this->behavior_class = $behaviorClass;
    }

    /**
     * Get behavior_class
     *
     * @return string $behaviorClass
     */
    public function getBehaviorClass()
    {
        return $this->behavior_class;
    }

    /**
     * Set validated_at
     *
     * @param datetime $validatedAt
     */
    public function setValidatedAt($validatedAt)
    {
        $this->validated_at = $validatedAt;
    }

    /**
     * Get validated_at
     *
     * @return datetime $validatedAt
     */
    public function getValidatedAt()
    {
        return $this->validated_at;
    }

    /**
     * Add product
     *
     * @param Sonata\Bundle\ProductBundle\Entity\BaseProduct $product
     */
    public function addProduct(\Application\ProductBundle\Entity\Product $product)
    {
        $this->product[] = $product;
    }

    /**
     * Get product
     *
     * @return Doctrine\Common\Collections\Collection $product
     */
    public function getProduct()
    {
        return $this->product;
    }
    /**
     * @var string $product_type
     */
    private $product_type;

    /**
     * Set product_type
     *
     * @param string $productType
     */
    public function setProductType($productType)
    {
        $this->product_type = $productType;
    }

    /**
     * Get product_type
     *
     * @return string $productType
     */
    public function getProductType()
    {
        return $this->product_type;
    }
}