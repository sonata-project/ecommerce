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
     * @var integer $id
     */
    private $id;

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
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }











}