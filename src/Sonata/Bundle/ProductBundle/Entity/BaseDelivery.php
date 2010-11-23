<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\ProductBundle\Entity;

/**
 * Sonata\Bundle\ProductBundle\Entity\BaseDelivery
 */
abstract class BaseDelivery
{
    /**
     * @var integer $product_id
     */
    protected $product_id;

    /**
     * @var string $class_name
     */
    protected $class_name;

    /**
     * @var boolean $per_item
     */
    protected $per_item;

    /**
     * @var string $country
     */
    protected $country;

    /**
     * @var string $zone
     */
    protected $zone;

    /**
     * @var boolean $enabled
     */
    protected $enabled;

    /**
     * @var datetime $updated_at
     */
    protected $updated_at;

    /**
     * @var datetime $created_at
     */
    protected $created_at;

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
     * Set updated_at
     *
     * @param datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
    }

    /**
     * Get updated_at
     *
     * @return datetime $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set created_at
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    }

    /**
     * Get created_at
     *
     * @return datetime $createdAt
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }
}