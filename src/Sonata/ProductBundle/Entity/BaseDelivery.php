<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Entity;

/**
 * Sonata\ProductBundle\Entity\BaseDelivery
 */
abstract class BaseDelivery
{
    /**
     * @var string $code
     */
    protected $code;

    /**
     * @var boolean $per_item
     */
    protected $perItem;

    /**
     * @var string $country
     */
    protected $countryCode;

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
    protected $updatedAt;

    /**
     * @var datetime $created_at
     */
    protected $createdAt;

    protected $product;
    
    /**
     * Set productId
     *
     * @param integer $productId
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * Get productId
     *
     * @return integer $productId
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set class_name
     *
     * @param string $className
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Get class_name
     *
     * @return string $className
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set per_item
     *
     * @param boolean $perItem
     */
    public function setPerItem($perItem)
    {
        $this->perItem = $perItem;
    }

    /**
     * Get per_item
     *
     * @return boolean $perItem
     */
    public function getPerItem()
    {
        return $this->perItem;
    }

    /**
     * Set country
     *
     * @param string $country
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
    }

    /**
     * Get country
     *
     * @return string $country
     */
    public function getCountryCode()
    {
        return $this->countryCode;
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
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updated_at
     *
     * @return datetime $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set created_at
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get created_at
     *
     * @return datetime $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function prePresist()
    {
        $this->createdAt = new \DateTime;
        $this->updatedAt = new \DateTime;
    }

    public function preUpdate()
    {
        $this->updatedAt = new \DateTime;
    }
}