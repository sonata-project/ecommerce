<?php

namespace Sonata\Bundle\ProductBundle\Entity;

/**
 * Sonata\Bundle\ProductBundle\Entity\Delivery
 */
class Delivery
{
    /**
     * @var integer $product_id
     */
    private $product_id;

    /**
     * @var string $class_name
     */
    private $class_name;

    /**
     * @var boolean $per_item
     */
    private $per_item;

    /**
     * @var string $country
     */
    private $country;

    /**
     * @var string $zone
     */
    private $zone;

    /**
     * @var boolean $enabled
     */
    private $enabled;

    /**
     * @var integer $id
     */
    private $id;

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
     * Set class_name
     *
     * @param string $className
     */
    public function setClassName($className)
    {
        $this->class_name = $className;
    }

    /**
     * Get class_name
     *
     * @return string $className
     */
    public function getClassName()
    {
        return $this->class_name;
    }

    /**
     * Set per_item
     *
     * @param boolean $perItem
     */
    public function setPerItem($perItem)
    {
        $this->per_item = $perItem;
    }

    /**
     * Get per_item
     *
     * @return boolean $perItem
     */
    public function getPerItem()
    {
        return $this->per_item;
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
     * Set zone
     *
     * @param string $zone
     */
    public function setZone($zone)
    {
        $this->zone = $zone;
    }

    /**
     * Get zone
     *
     * @return string $zone
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * Get enabled
     *
     * @return boolean $enabled
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }











}