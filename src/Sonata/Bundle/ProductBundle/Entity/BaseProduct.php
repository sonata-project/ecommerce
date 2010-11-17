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
 * Sonata\Bundle\ProductBundle\Entity\BaseProduct
 */
abstract class BaseProduct implements \Sonata\Component\Product\ProductInterface
{
    /**
     * @var text $sku
     */
    protected $sku;

    /**
     * @var text $slug
     */
    protected $slug;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var text $description
     */
    protected $description;

    /**
     * @var decimal $price
     */
    protected $price;

    /**
     * @var decimal $vat
     */
    protected $vat;

    /**
     * @var integer $stock
     */
    protected $stock;

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
     * @var Sonata\Bundle\ProductBundle\Entity\BasePackage
     */
    protected $package;

    /**
     * @var Sonata\Bundle\ProductBundle\Entity\BaseDelivery
     */
    protected $delivery;

    protected $parent_id;

    protected $options = array();

    /**
     * Set sku
     *
     * @param text $sku
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
    }

    /**
     * Get sku
     *
     * @return text $sku
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Set slug
     *
     * @param text $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Get slug
     *
     * @return text $slug
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
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
     * Set stock
     *
     * @param integer $stock
     */
    public function setStock($stock)
    {
        $this->stock = $stock;
    }

    /**
     * Get stock
     *
     * @return integer $stock
     */
    public function getStock()
    {
        return $this->stock;
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
     * Add package
     *
     * @param Sonata\Bundle\ProductBundle\Entity\BasePackage $package
     */
    public function addPackage(\Sonata\Bundle\ProductBundle\Entity\BasePackage $package)
    {
        $this->package[] = $package;
    }

    /**
     * Get package
     *
     * @return Doctrine\Common\Collections\Collection $package
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * Add delivery
     *
     * @param Sonata\Bundle\ProductBundle\Entity\BaseDelivery $delivery
     */
    public function addDelivery(\Sonata\Bundle\ProductBundle\Entity\BaseDelivery $delivery)
    {
        $this->delivery[] = $delivery;
    }

    /**
     * Get delivery
     *
     * @return Doctrine\Common\Collections\Collection $delivery
     */
    public function getDelivery()
    {
        return $this->delivery;
    }


    /**
     * @abstract
     * @return string the product name
     */
    public function getParent() {

        return $this->parent;
    }

    /**
     * @abstract
     * @return
     */
    public function setParent($parent) {
        $this->parent = $parent;
    }


    /**
     * @abstract
     * @return string the product name
     */
    public function getOptions() {

        return $this->options;
    }

    /**
     * @abstract
     * @return
     */
    public function setOptions($options) {
        $this->$options = $options;
    }

    public function getParentId() {
        return $this->parent_id;
    }

    public function setParentId($parent_id) {
        $this->parent_id = $parent_id;
    }

    public function isVariation() {

        return $this->parent_id > 0;
    }

    /**
     * @var type $type
     */
    protected $type;

    /**
     * Set type
     *
     * @param type $type
     */
    public function setType(\type $type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return type $type
     */
    public function getType()
    {
        return $this->type;
    }
}