<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Sonata\Component\Product\DeliveryInterface;
use Sonata\Component\Product\PackageInterface;
use Sonata\Component\Product\ProductCategoryInterface;
use Sonata\Component\Product\ProductCollectionInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\MediaBundle\Model\GalleryInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

abstract class BaseProduct implements ProductInterface
{
    /**
     * @var string
     */
    protected $sku;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $priceIncludingVat;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $rawDescription;

    /**
     * @var string
     */
    protected $descriptionFormatter;

    /**
     * @var string
     */
    protected $shortDescription;

    /**
     * @var string
     */
    protected $rawShortDescription;

    /**
     * @var string
     */
    protected $shortDescriptionFormatter;

    /**
     * @var float
     */
    protected $price;

    /**
     * @var float
     */
    protected $vatRate;

    /**
     * @var int
     */
    protected $stock;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var ArrayCollection
     */
    protected $packages;

    /**
     * @var ArrayCollection
     */
    protected $deliveries;

    /**
     * @var ArrayCollection
     */
    protected $productCategories;

    /**
     * @var MediaInterface
     */
    protected $image;

    /**
     * @var GalleryInterface
     */
    protected $gallery;

    /**
     * @var ProductInterface
     */
    protected $parent;

    /**
     * @var ArrayCollection
     */
    protected $variations;

    /**
     * @var ArrayCollection
     */
    protected $enabledVariations;

    /**
     * @var ArrayCollection
     */
    protected $productCollections;

    /**
     * @var array
     */
    protected $options = [];

    public function __construct()
    {
        $this->packages = new ArrayCollection();
        $this->deliveries = new ArrayCollection();
        $this->productCategories = new ArrayCollection();
        $this->productCollections = new ArrayCollection();
        $this->variations = new ArrayCollection();
        $this->enabledVariations = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->getName();
    }

    public function addProductCollection(ProductCollectionInterface $productCollection): void
    {
        $productCollection->setProduct($this);

        $this->productCollections->add($productCollection);
    }

    public function removeProductCollection(ProductCollectionInterface $productCollection): void
    {
        if ($this->productCollections->contains($productCollection)) {
            $this->productCollections->removeElement($productCollection);
        }
    }

    public function getProductCollections()
    {
        return $this->productCollections;
    }

    public function setProductCollections(ArrayCollection $productCollections): void
    {
        $this->productCollections = $productCollections;
    }

    public function getCollections()
    {
        $collections = new ArrayCollection();

        foreach ($this->productCollections as $productCollection) {
            if (!$collections->contains($productCollection)) {
                $collections->add($productCollection->getCollection());
            }
        }

        return $collections;
    }

    public function setSku($sku): void
    {
        $this->sku = $sku;
    }

    public function getSku()
    {
        return $this->sku;
    }

    public function setSlug($slug): void
    {
        $slug = Slugify::create()->slugify(trim((string) $slug));

        $this->slug = !empty($slug) ? $slug : 'n-a';
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setName($name): void
    {
        $this->name = $name;

        if (!$this->getSlug()) {
            $this->setSlug($name);
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function setDescription($description): void
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setRawDescription($rawDescription): void
    {
        $this->rawDescription = $rawDescription;
    }

    public function getRawDescription()
    {
        return $this->rawDescription;
    }

    public function setDescriptionFormatter($descriptionFormatter): void
    {
        $this->descriptionFormatter = $descriptionFormatter;
    }

    public function getDescriptionFormatter()
    {
        return $this->descriptionFormatter;
    }

    public function setShortDescription($shortDescription): void
    {
        $this->shortDescription = $shortDescription;
    }

    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    public function setRawShortDescription($rawShortDescription): void
    {
        $this->rawShortDescription = $rawShortDescription;
    }

    public function getRawShortDescription()
    {
        return $this->rawShortDescription;
    }

    public function setShortDescriptionFormatter($shortDescriptionFormatter): void
    {
        $this->shortDescriptionFormatter = $shortDescriptionFormatter;
    }

    public function getShortDescriptionFormatter()
    {
        return $this->shortDescriptionFormatter;
    }

    public function setPrice($price): void
    {
        $this->price = $price;
    }

    public function getPrice($vat = false)
    {
        return $this->price;
    }

    public function setUnitPrice($unitPrice): void
    {
        $this->setPrice($unitPrice);
    }

    public function getUnitPrice($vat = false)
    {
        return $this->getPrice($vat);
    }

    public function setQuantity($quantity): void
    {
        throw new \InvalidMethodCallException('This method is not used');
    }

    public function getQuantity(): void
    {
        throw new \InvalidMethodCallException('This method is not used');
    }

    public function setVatRate($vatRate): void
    {
        $this->vatRate = $vatRate;
    }

    public function getVatRate()
    {
        return $this->vatRate;
    }

    public function setStock($stock): void
    {
        $this->stock = $stock;
    }

    public function getStock()
    {
        return $this->stock;
    }

    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setUpdatedAt(\DateTime $updatedAt = null): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setCreatedAt(\DateTime $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function addPackage(PackageInterface $package): void
    {
        $package->setProduct($this);

        $this->packages->add($package);
    }

    public function removePackage(PackageInterface $package): void
    {
        if ($this->packages->contains($package)) {
            $this->packages->removeElement($package);
        }
    }

    public function getPackages()
    {
        return $this->packages;
    }

    public function setPackages(ArrayCollection $packages): void
    {
        $this->packages = $packages;
    }

    public function addDelivery(DeliveryInterface $delivery): void
    {
        $delivery->setProduct($this);

        $this->deliveries->add($delivery);
    }

    public function removeDelivery(DeliveryInterface $delivery): void
    {
        if ($this->deliveries->contains($delivery)) {
            $this->deliveries->removeElement($delivery);
        }
    }

    public function getDeliveries()
    {
        return $this->deliveries;
    }

    public function setDeliveries(ArrayCollection $deliveries): void
    {
        $this->deliveries = $deliveries;
    }

    public function addDeliverie(DeliveryInterface $delivery): void
    {
        $this->addDelivery($delivery);
    }

    public function removeDeliverie(DeliveryInterface $delivery): void
    {
        $this->removeDelivery($delivery);
    }

    public function addProductCategorie(ProductCategoryInterface $productCategory): void
    {
        $this->addProductCategory($productCategory);
    }

    public function removeProductCategorie(ProductCategoryInterface $productCategory): void
    {
        $this->removeProductCategory($productCategory);
    }

    public function addProductCategory(ProductCategoryInterface $productCategory): void
    {
        $productCategory->setProduct($this);

        $this->productCategories->add($productCategory);
    }

    public function removeProductCategory(ProductCategoryInterface $productCategory): void
    {
        if ($this->productCategories->contains($productCategory)) {
            $this->productCategories->removeElement($productCategory);
        }
    }

    public function getProductCategories()
    {
        return $this->productCategories;
    }

    public function setProductCategories(ArrayCollection $productCategories): void
    {
        $this->productCategories = $productCategories;
    }

    public function getCategories()
    {
        $categories = new ArrayCollection();

        foreach ($this->productCategories as $productCategory) {
            if (!$categories->contains($productCategory)) {
                $categories->add($productCategory->getCategory());
            }
        }

        return $categories;
    }

    public function getMainCategory()
    {
        foreach ($this->getProductCategories() as $productCategory) {
            if ($productCategory->getMain()) {
                return $productCategory->getCategory();
            }
        }
    }

    public function addVariation(ProductInterface $variation): void
    {
        $variation->setParent($this);

        $this->variations->add($variation);
    }

    public function removeVariation(ProductInterface $variation): void
    {
        if ($this->variations->contains($variation)) {
            $this->variations->removeElement($variation);
        }
    }

    public function getVariations()
    {
        return $this->variations;
    }

    public function setVariations(ArrayCollection $variations): void
    {
        $this->variations = $variations;
    }

    public function hasVariations()
    {
        return \count($this->variations) > 0;
    }

    public function setImage(MediaInterface $image = null): void
    {
        $this->image = $image;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setGallery(GalleryInterface $gallery = null): void
    {
        $this->gallery = $gallery;
    }

    public function getGallery()
    {
        return $this->gallery;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent(ProductInterface $parent): void
    {
        $this->parent = $parent;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function isMaster()
    {
        return null === $this->parent;
    }

    public function isVariation()
    {
        return null !== $this->parent;
    }

    public function isEnabled()
    {
        return $this->getEnabled();
    }

    public function isSalable()
    {
        // Product is enabled and is a variation or a single product.
        return $this->isEnabled() && ($this->isVariation() || !$this->hasVariations());
    }

    public function isRecurrentPayment()
    {
        return false;
    }

    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function prePersist(): void
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function setPriceIncludingVat($priceIncludingVat): void
    {
        $this->priceIncludingVat = $priceIncludingVat;
    }

    public function isPriceIncludingVat()
    {
        return $this->priceIncludingVat;
    }

    public function toArray()
    {
        $baseArrayRep = [
            'sku' => $this->sku,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'rawDescription' => $this->rawDescription,
            'descriptionFormatter' => $this->descriptionFormatter,
            'shortDescription' => $this->shortDescription,
            'rawShortDescription' => $this->rawShortDescription,
            'shortDescriptionFormatter' => $this->shortDescriptionFormatter,
            'price' => $this->price,
            'vatRate' => $this->vatRate,
            'stock' => $this->stock,
            'enabled' => $this->enabled,
            'options' => $this->options,
        ];

        return $baseArrayRep;
    }

    public function fromArray($array): void
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($array as $key => $value) {
            $accessor->setValue($this, $key, $value);
        }
    }

    public function hasOneMainCategory()
    {
        if (0 === $this->getCategories()->count()) {
            return false;
        }

        $has = false;

        foreach ($this->getProductCategories() as $productCategory) {
            if ($productCategory->getMain()) {
                if ($has) {
                    $has = false;

                    break;
                }

                $has = true;
            }
        }

        return $has;
    }

    public function validateOneMainCategory(ExecutionContextInterface $context): void
    {
        if (0 === $this->getCategories()->count()) {
            return;
        }

        if (!$this->hasOneMainCategory()) {
            $context->addViolation('sonata.product.must_have_one_main_category');
        }
    }
}
