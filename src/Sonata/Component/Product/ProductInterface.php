<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Product;

use Sonata\MediaBundle\Model\MediaInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\ExecutionContext;

interface ProductInterface
{
    /**
     * Get id.
     *
     * @return integer
     */
    public function getId();

    /**
     * Set sku.
     *
     * @param string $sku
     */
    public function setSku($sku);

    /**
     * Get sku.
     *
     * @return string
     */
    public function getSku();

    /**
     * Set slug.
     *
     * @param string $slug
     */
    public function setSlug($slug);

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug();

    /**
     * Set name.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Get name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set description.
     *
     * @param string $description
     */
    public function setDescription($description);

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set RAW description.
     *
     * @param string $rawDescription
     */
    public function setRawDescription($rawDescription);

    /**
     * Get RAW description.
     *
     * @return string
     */
    public function getRawDescription();

    /**
     * Set description formatter.
     *
     * @param string $descriptionFormatter
     */
    public function setDescriptionFormatter($descriptionFormatter);

    /**
     * Get description formatter.
     *
     * @return string
     */
    public function getDescriptionFormatter();

    /**
     * Set price.
     *
     * @param float $price
     */
    public function setPrice($price);

    /**
     * Get price.
     *
     * @return float
     */
    public function getPrice();

    /**
     * Set vat.
     *
     * @param float $vat
     */
    public function setVat($vat);

    /**
     * Get vat
     *
     * @return float
     */
    public function getVat();

    /**
     * Set stock.
     *
     * @param integer $stock
     */
    public function setStock($stock);

    /**
     * Get stock.
     *
     * @return integer
     */
    public function getStock();

    /**
     * Set enabled.
     *
     * @param boolean $enabled
     */
    public function setEnabled($enabled);

    /**
     * Get enabled.
     *
     * @return boolean
     */
    public function getEnabled();

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null);

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Add a Package to collection.
     *
     * @param PackageInterface $package
     */
    public function addPackage(PackageInterface $package);

    /**
     * Remove a Package from collection.
     *
     * @param PackageInterface $package
     */
    public function removePackage(PackageInterface $package);

    /**
     * Get Package collection.
     *
     * @return ArrayCollection
     */
    public function getPackages();

    /**
     * Set Package collection.
     *
     * @param ArrayCollection $packages
     */
    public function setPackages(ArrayCollection $packages);

    /**
     * Add a Delivery to collection.
     *
     * @param DeliveryInterface $delivery
     */
    public function addDelivery(DeliveryInterface $delivery);

    /**
     * Remove a Delivery from collection.
     *
     * @param DeliveryInterface $delivery
     */
    public function removeDelivery(DeliveryInterface $delivery);

    /**
     * Add a Delivery to collection. Alias for addDelivery needed by the AdminBundle
     *
     * @param DeliveryInterface $delivery
     */
    public function addDeliverie(DeliveryInterface $delivery);

    /**
     * Remove a Delivery from collection. Alias for removeDelivery needed by the AdminBundle
     *
     * @param DeliveryInterface $delivery
     */
    public function removeDeliverie(DeliveryInterface $delivery);

    /**
     * Get Delivery collection.
     *
     * @return ArrayCollection
     */
    public function getDeliveries();

    /**
     * Set Delivery collection.
     *
     * @param ArrayCollection $deliveries
     */
    public function setDeliveries(ArrayCollection $deliveries);

    /**
     * Add a ProductCategory to collection.
     *
     * @param ProductCategoryInterface $productCategory
     */
    public function addProductCategory(ProductCategoryInterface $productCategory);

    /**
     * Remove a ProductCategory from collection.
     *
     * @param ProductCategoryInterface $productCategory
     */
    public function removeProductCategory(ProductCategoryInterface $productCategory);

    /**
     * Add a ProductCategory to collection. Alias for addProductCategory needed for AdminBundle.
     *
     * @param ProductCategoryInterface $productCategory
     */
    public function addProductCategorie(ProductCategoryInterface $productCategory);

    /**
     * Remove a ProductCategory from collection. Alias for removeProductCategory needed for AdminBundle.
     *
     * @param ProductCategoryInterface $productCategory
     */
    public function removeProductCategorie(ProductCategoryInterface $productCategory);

    /**
     * Get ProductCategories collection.
     *
     * @return ArrayCollection
     */
    public function getProductCategories();

    /**
     * Set ProductCategory collection.
     *
     * @param ArrayCollection $productCategories
     */
    public function setProductCategories(ArrayCollection $productCategories);

    /**
     * Add a ProductCollection to collection.
     *
     * @param ProductCollectionInterface $productCollection
     */
    public function addProductCollection(ProductCollectionInterface $productCollection);

    /**
     * Remove a ProductCollection from collection.
     *
     * @param ProductCollectionInterface $productCollection
     */
    public function removeProductCollection(ProductCollectionInterface $productCollection);

    /**
     * Get ProductCollections collection.
     *
     * @return ArrayCollection
     */
    public function getProductCollections();

    /**
     * Set ProductCollection collection.
     *
     * @param ArrayCollection $productCollections
     */
    public function setProductCollections(ArrayCollection $productCollections);

    /**
     * Get Collections collection.
     *
     * @return ArrayCollection
     */
    public function getCollections();

    /**
     * Get Categories collection.
     *
     * @return ArrayCollection
     */
    public function getCategories();

    /**
     * Add a variation to collection.
     *
     * @param ProductInterface $variation
     */
    public function addVariation(ProductInterface $variation);

    /**
     * Remove a variation from collection.
     *
     * @param ProductInterface $variation
     */
    public function removeVariation(ProductInterface $variation);

    /**
     * Get variation collection.
     *
     * @return ArrayCollection
     */
    public function getVariations();

    /**
     * Set variation collection.
     *
     * @param ArrayCollection $variations
     */
    public function setVariations(ArrayCollection $variations);

    /**
     * Set Media.
     *
     * @param MediaInterface $image
     */
    public function setImage(MediaInterface $image = null);

    /**
     * Get Media.
     *
     * @return MediaInterface
     */
    public function getImage();

    /**
     * Get parent Product.
     *
     * @return ProductInterface
     */
    public function getParent();

    /**
     * Set parent Product.
     *
     * @param ProductInterface $parent
     */
    public function setParent(ProductInterface $parent);

    /**
     * Get Product options.
     *
     * @return array
     */
    public function getOptions();

    /**
     * Set Product options.
     *
     * @param array $options
     */
    public function setOptions(array $options);

    /**
     * Get Product name.
     *
     * @return string
     */
    public function __toString();

    /**
     * Return true if the Product is a master (parent of variation(s) or single product).
     *
     * @return boolean
     */
    public function isMaster();

    /**
     * Return true if the Product is a variation, linked to a main Product.
     *
     * @return boolean
     */
    public function isVariation();

    /**
     * Return true if Product is enabled.
     *
     * @return boolean
     */
    public function isEnabled();

    /**
     * Return true if Product can be sold.
     *
     * @return boolean
     */
    public function isSalable();

    /**
     * Return true if the Product is recurrent.
     *
     * @return boolean
     */
    public function isRecurrentPayment();

    /**
     * Returns Product base data as an array.
     *
     * @return array
     */
    public function toArray();

    /**
     * Populate entity from an array.
     *
     * @param array $array
     */
    public function fromArray($array);

    /**
     * Returns if product has one main category
     *
     * @return bool
     */
    public function hasOneMainCategory();

    /**
     * Validates if product has one main category
     *
     * @param ExecutionContext $context
     *
     * @return void
     */
    public function validateOneMainCategory(ExecutionContext $context);
}
