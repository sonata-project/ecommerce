<?php

namespace Sonata\Bundle\ProductBundle\Entity;

/**
 * Sonata\Bundle\ProductBundle\Entity\Package
 */
class Package
{
    /**
     * @var integer $product_id
     */
    private $product_id;

    /**
     * @var decimal $width
     */
    private $width;

    /**
     * @var decimal $height
     */
    private $height;

    /**
     * @var decimal $length
     */
    private $length;

    /**
     * @var decimal $weight
     */
    private $weight;

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
     * Set width
     *
     * @param decimal $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * Get width
     *
     * @return decimal $width
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param decimal $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * Get height
     *
     * @return decimal $height
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set length
     *
     * @param decimal $length
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * Get length
     *
     * @return decimal $length
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Set weight
     *
     * @param decimal $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * Get weight
     *
     * @return decimal $weight
     */
    public function getWeight()
    {
        return $this->weight;
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