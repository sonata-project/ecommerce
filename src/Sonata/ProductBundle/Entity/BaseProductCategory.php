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
 * Sonata\ProductBundle\Entity\BaseProductCategory
 */
abstract class BaseProductCategory
{
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

    /**
     * @var Application\Sonata\ProductBundle\Entity\Product
     */
    protected $product;

    /**
     * @var Application\Sonata\ProductBundle\Entity\Category
     */
    protected $category;

    /**
     * Set Product
     *
     * @param Application\Sonata\ProductBundle\Entity\Product $product
     */
    public function setProduct(\Application\Sonata\ProductBundle\Entity\Product $product)
    {
        $this->product = $product;
    }

    /**
     * Get Product
     *
     * @return Application\Sonata\ProductBundle\Entity\Product $product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set Category
     *
     * @param Application\Sonata\ProductBundle\Entity\Category $category
     */
    public function setCategory(\Application\Sonata\ProductBundle\Entity\Category $category)
    {
        $this->category = $category;
    }

    /**
     * Get Category
     *
     * @return Application\Sonata\ProductBundle\Entity\Category $category
     */
    public function getCategory()
    {
        return $this->category;
    }
}