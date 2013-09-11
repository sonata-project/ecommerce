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

use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\DeliveryInterface;
use Sonata\MediaBundle\Model\MediaInterface;

use Sonata\Component\Product\ProductCategoryInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Sonata\ProductBundle\Entity\BaseProduct
 */
abstract class BaseProduct implements ProductInterface
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
     * @var text $rawDescription
     */
    protected $rawDescription;

    /**
     * @var text $descriptionFormatter
     */
    protected $descriptionFormatter;

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
     * @var \DateTime $updatedAt
     */
    protected $updatedAt;

    /**
     * @var \DateTime $createdAt
     */
    protected $createdAt;

    protected $image;

    /**
     * @var \Sonata\ProductBundle\Entity\BasePackage
     */
    protected $package;

    /**
     * @var Collection
     */
    protected $deliveries;

    protected $parent;

    protected $options = array();

    protected $variations = array();

    /**
     * @var Collection
     */
    protected $productCategories;

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
     * Set RAW description.
     *
     * @param text $rawDescription
     */
    public function setRawDescription($rawDescription)
    {
        $this->rawDescription = $rawDescription;
    }

    /**
     * Get RAW description.
     *
     * @return text $rawDescription
     */
    public function getRawDescription()
    {
        return $this->rawDescription;
    }

    /**
     * Set description formatter.
     *
     * @param text $descriptionFormatter
     */
    public function setDescriptionFormatter($descriptionFormatter)
    {
        $this->descriptionFormatter = $descriptionFormatter;
    }

    /**
     * Get description formatter.
     *
     * @return text $descriptionFormatter
     */
    public function getDescriptionFormatter()
    {
        return $this->descriptionFormatter;
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Add package
     *
     * @param \Sonata\ProductBundle\Entity\BasePackage $package
     */
    public function addPackage($package)
    {
        $this->package[] = $package;
    }

    /**
     * Get package
     *
     * @return \Doctrine\Common\Collections\Collection $package
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * Add delivery
     *
     * @param \Sonata\Component\Product\DeliveryInterface $delivery
     */
    public function addDelivery(DeliveryInterface $delivery)
    {
        $delivery->setProduct($this);
        $this->deliveries[] = $delivery;
    }

    /**
     * Adds a delivery (grammar mixup)
     *
     * @param DeliveryInterface $delivery
     */
    public function addDeliverie(DeliveryInterface $delivery)
    {
        $this->addDelivery($delivery);
    }

    /**
     * Remove delivery
     *
     * @param \Sonata\Component\Product\DeliveryInterface $delivery
     */
    public function removeDelivery(DeliveryInterface $delivery)
    {
        if ($this->deliveries->contains($delivery)) {
            $this->deliveries->remove($delivery);
        }
    }

    /**
     * Get delivery
     *
     * @return \Doctrine\Common\Collections\Collection $delivery
     */
    public function getDeliveries()
    {
        return $this->deliveries;
    }

    /**
     * Set deliveries
     *
     * @param Collection $deliveries
     */
    public function setDeliveries(Collection $deliveries)
    {
        $this->deliveries = $deliveries;
    }

    /**
     * @return string the product name
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param \Sonata\Component\Product\ProductInterface $parent
     */
    public function setParent(ProductInterface $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return string the product name
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set product options
     *
     * @param array $options
     *
     * @return void
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    public function isVariation()
    {
        return $this->getParent() !== null;
    }

    /**
     * Add ProductCategories
     *
     * @param \Sonata\Component\Product\ProductCategoryInterface $productCategory
     */
    public function addProductCategory(ProductCategoryInterface $productCategory)
    {
        $productCategory->setProduct($this);
        $this->productCategories[] = $productCategory;
    }

    /**
     * Add ProductCategory
     *
     * @param ProductCategoryInterface $productCategory
     */
    public function addProductCategorie(ProductCategoryInterface $productCategory)
    {
        $this->addProductCategory($productCategory);
    }

    /**
     * Remove ProductCategories
     *
     * @param \Sonata\Component\Product\ProductCategoryInterface $productCategory
     */
    public function removeProductCategory(ProductCategoryInterface $productCategory)
    {
        if ($this->productCategories->has($productCategory)) {
            $this->productCategories->remove($productCategory);
        }
    }

    /**
     * Set ProductCategories
     *
     * @param Collection $productCategories
     */
    public function setProductCategory(Collection $productCategories)
    {
        $this->productCategories = $productCategories;
    }

    /**
     * Get ProductCategories
     *
     * @return Doctrine\Common\Collections\Collection $productCategories
     */
    public function getProductCategories()
    {
        return $this->productCategories;
    }

    /**
     * Tells if product is enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->getEnabled();
    }

    /**
     * Set image
     *
     * @param \Sonata\MediaBundle\Model\MediaInterface $image
     */
    public function setImage(MediaInterface $image = null)
    {
        $this->image = $image;
    }

    /**
     * Get image
     *
     * @return Application\Sonata\MediaBundle\Entity\Media $image
     */
    public function getImage()
    {
        return $this->image;
    }

    public function setVariations($variations)
    {
        $this->variations = $variations;
    }

    public function getVariations()
    {
        return $this->variations;
    }

    public function addVariation($variation)
    {
        $this->variations[] = $variation;
    }

    public function __toString()
    {
        return (string) $this->getName();
    }

    public function preUpdate()
    {
        $this->updatedAt = new \DateTime;
    }

    public function prePersist()
    {
        $this->createdAt = new \DateTime;
        $this->updatedAt = new \DateTime;
    }

    public function isRecurrentPayment()
    {
        return false;
    }
}
