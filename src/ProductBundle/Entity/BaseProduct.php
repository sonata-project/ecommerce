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

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string) $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function addProductCollection(ProductCollectionInterface $productCollection): void
    {
        $productCollection->setProduct($this);

        $this->productCollections->add($productCollection);
    }

    /**
     * {@inheritdoc}
     */
    public function removeProductCollection(ProductCollectionInterface $productCollection): void
    {
        if ($this->productCollections->contains($productCollection)) {
            $this->productCollections->removeElement($productCollection);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getProductCollections()
    {
        return $this->productCollections;
    }

    /**
     * {@inheritdoc}
     */
    public function setProductCollections(ArrayCollection $productCollections): void
    {
        $this->productCollections = $productCollections;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function setSku($sku): void
    {
        $this->sku = $sku;
    }

    /**
     * {@inheritdoc}
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * {@inheritdoc}
     */
    public function setSlug($slug): void
    {
        $slug = Slugify::create()->slugify(trim((string) $slug));

        $this->slug = !empty($slug) ? $slug : 'n-a';
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name): void
    {
        $this->name = $name;

        if (!$this->getSlug()) {
            $this->setSlug($name);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setRawDescription($rawDescription): void
    {
        $this->rawDescription = $rawDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function getRawDescription()
    {
        return $this->rawDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescriptionFormatter($descriptionFormatter): void
    {
        $this->descriptionFormatter = $descriptionFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescriptionFormatter()
    {
        return $this->descriptionFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function setShortDescription($shortDescription): void
    {
        $this->shortDescription = $shortDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function setRawShortDescription($rawShortDescription): void
    {
        $this->rawShortDescription = $rawShortDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function getRawShortDescription()
    {
        return $this->rawShortDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function setShortDescriptionFormatter($shortDescriptionFormatter): void
    {
        $this->shortDescriptionFormatter = $shortDescriptionFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function getShortDescriptionFormatter()
    {
        return $this->shortDescriptionFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice($vat = false)
    {
        return $this->price;
    }

    /**
     * {@inheritdoc}
     */
    public function setUnitPrice($unitPrice): void
    {
        $this->setPrice($unitPrice);
    }

    /**
     * {@inheritdoc}
     */
    public function getUnitPrice($vat = false)
    {
        return $this->getPrice($vat);
    }

    /**
     * {@inheritdoc}
     */
    public function setQuantity($quantity): void
    {
        throw new \InvalidMethodCallException('This method is not used');
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantity(): void
    {
        throw new \InvalidMethodCallException('This method is not used');
    }

    /**
     * {@inheritdoc}
     */
    public function setVatRate($vatRate): void
    {
        $this->vatRate = $vatRate;
    }

    /**
     * {@inheritdoc}
     */
    public function getVatRate()
    {
        return $this->vatRate;
    }

    /**
     * {@inheritdoc}
     */
    public function setStock($stock): void
    {
        $this->stock = $stock;
    }

    /**
     * {@inheritdoc}
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt = null): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function addPackage(PackageInterface $package): void
    {
        $package->setProduct($this);

        $this->packages->add($package);
    }

    /**
     * {@inheritdoc}
     */
    public function removePackage(PackageInterface $package): void
    {
        if ($this->packages->contains($package)) {
            $this->packages->removeElement($package);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * {@inheritdoc}
     */
    public function setPackages(ArrayCollection $packages): void
    {
        $this->packages = $packages;
    }

    /**
     * {@inheritdoc}
     */
    public function addDelivery(DeliveryInterface $delivery): void
    {
        $delivery->setProduct($this);

        $this->deliveries->add($delivery);
    }

    /**
     * {@inheritdoc}
     */
    public function removeDelivery(DeliveryInterface $delivery): void
    {
        if ($this->deliveries->contains($delivery)) {
            $this->deliveries->removeElement($delivery);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDeliveries()
    {
        return $this->deliveries;
    }

    /**
     * {@inheritdoc}
     */
    public function setDeliveries(ArrayCollection $deliveries): void
    {
        $this->deliveries = $deliveries;
    }

    /**
     * {@inheritdoc}
     */
    public function addDeliverie(DeliveryInterface $delivery): void
    {
        $this->addDelivery($delivery);
    }

    /**
     * {@inheritdoc}
     */
    public function removeDeliverie(DeliveryInterface $delivery): void
    {
        $this->removeDelivery($delivery);
    }

    /**
     * {@inheritdoc}
     */
    public function addProductCategorie(ProductCategoryInterface $productCategory): void
    {
        $this->addProductCategory($productCategory);
    }

    /**
     * {@inheritdoc}
     */
    public function removeProductCategorie(ProductCategoryInterface $productCategory): void
    {
        $this->removeProductCategory($productCategory);
    }

    /**
     * {@inheritdoc}
     */
    public function addProductCategory(ProductCategoryInterface $productCategory): void
    {
        $productCategory->setProduct($this);

        $this->productCategories->add($productCategory);
    }

    /**
     * {@inheritdoc}
     */
    public function removeProductCategory(ProductCategoryInterface $productCategory): void
    {
        if ($this->productCategories->contains($productCategory)) {
            $this->productCategories->removeElement($productCategory);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getProductCategories()
    {
        return $this->productCategories;
    }

    /**
     * {@inheritdoc}
     */
    public function setProductCategories(ArrayCollection $productCategories): void
    {
        $this->productCategories = $productCategories;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function getMainCategory()
    {
        foreach ($this->getProductCategories() as $productCategory) {
            if ($productCategory->getMain()) {
                return $productCategory->getCategory();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addVariation(ProductInterface $variation): void
    {
        $variation->setParent($this);

        $this->variations->add($variation);
    }

    /**
     * {@inheritdoc}
     */
    public function removeVariation(ProductInterface $variation): void
    {
        if ($this->variations->contains($variation)) {
            $this->variations->removeElement($variation);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getVariations()
    {
        return $this->variations;
    }

    /**
     * {@inheritdoc}
     */
    public function setVariations(ArrayCollection $variations): void
    {
        $this->variations = $variations;
    }

    /**
     * {@inheritdoc}
     */
    public function hasVariations()
    {
        return count($this->variations) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function setImage(MediaInterface $image = null): void
    {
        $this->image = $image;
    }

    /**
     * {@inheritdoc}
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * {@inheritdoc}
     */
    public function setGallery(GalleryInterface $gallery = null): void
    {
        $this->gallery = $gallery;
    }

    /**
     * {@inheritdoc}
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(ProductInterface $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function isMaster()
    {
        return null === $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function isVariation()
    {
        return null !== $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->getEnabled();
    }

    /**
     * {@inheritdoc}
     */
    public function isSalable()
    {
        // Product is enabled and is a variation or a single product.
        return $this->isEnabled() && ($this->isVariation() || !$this->hasVariations());
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function setPriceIncludingVat($priceIncludingVat): void
    {
        $this->priceIncludingVat = $priceIncludingVat;
    }

    /**
     * {@inheritdoc}
     */
    public function isPriceIncludingVat()
    {
        return $this->priceIncludingVat;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function fromArray($array): void
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($array as $key => $value) {
            $accessor->setValue($this, $key, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasOneMainCategory()
    {
        if (0 == $this->getCategories()->count()) {
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

    /**
     * {@inheritdoc}
     */
    public function validateOneMainCategory(ExecutionContextInterface $context): void
    {
        if (0 == $this->getCategories()->count()) {
            return;
        }

        if (!$this->hasOneMainCategory()) {
            $context->addViolation('sonata.product.must_have_one_main_category');
        }
    }
}
