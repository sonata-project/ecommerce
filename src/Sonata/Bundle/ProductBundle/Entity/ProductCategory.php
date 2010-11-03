<?php

namespace Sonata\Bundle\ProductBundle\Entity;

/**
 * Sonata\Bundle\ProductBundle\Entity\ProductCategory
 */
class ProductCategory
{
    /**
     * @var integer $category_id
     */
    private $category_id;

    /**
     * @var integer $product_id
     */
    private $product_id;

    /**
     * @var boolean $enabled
     */
    private $enabled;

    /**
     * @var datetime $updated_at
     */
    private $updated_at;

    /**
     * @var datetime $created_at
     */
    private $created_at;

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var Sonata\Bundle\ProductBundle\Entity\Product
     */
    private $product;

    /**
     * @var Sonata\Bundle\ProductBundle\Entity\Category
     */
    private $category;

    /**
     * Set category_id
     *
     * @param integer $categoryId
     */
    public function setCategoryId($categoryId)
    {
        $this->category_id = $categoryId;
    }

    /**
     * Get category_id
     *
     * @return integer $categoryId
     */
    public function getCategoryId()
    {
        return $this->category_id;
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

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set product
     *
     * @param Sonata\Bundle\ProductBundle\Entity\Product $product
     */
    public function setProduct(\Sonata\Bundle\ProductBundle\Entity\Product $product)
    {
        $this->product = $product;
    }

    /**
     * Get product
     *
     * @return Sonata\Bundle\ProductBundle\Entity\Product $product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set category
     *
     * @param Sonata\Bundle\ProductBundle\Entity\Category $category
     */
    public function setCategory(\Sonata\Bundle\ProductBundle\Entity\Category $category)
    {
        $this->category = $category;
    }

    /**
     * Get category
     *
     * @return Sonata\Bundle\ProductBundle\Entity\Category $category
     */
    public function getCategory()
    {
        return $this->category;
    }
}