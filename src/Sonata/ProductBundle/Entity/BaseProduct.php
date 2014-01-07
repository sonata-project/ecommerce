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

use Sonata\Component\Product\ProductCollectionInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\PackageInterface;
use Sonata\Component\Product\DeliveryInterface;
use Sonata\Component\Product\ProductCategoryInterface;
use Sonata\MediaBundle\Model\GalleryInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\ExecutionContext;

/**
 * Sonata\ProductBundle\Entity\BaseProduct
 */
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
     * @var float
     */
    protected $price;

    /**
     * @var float
     */
    protected $vat;

    /**
     * @var integer
     */
    protected $stock;

    /**
     * @var boolean
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
    protected $options = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->packages           = new ArrayCollection();
        $this->deliveries         = new ArrayCollection();
        $this->productCategories  = new ArrayCollection();
        $this->productCollections = new ArrayCollection();
        $this->variations         = new ArrayCollection();
        $this->enabledVariations  = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function addProductCollection(ProductCollectionInterface $productCollection)
    {
        $productCollection->setProduct($this);

        $this->productCollections->add($productCollection);
    }

    /**
     * {@inheritdoc}
     */
    public function removeProductCollection(ProductCollectionInterface $productCollection)
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
    public function setProductCollections(ArrayCollection $productCollections)
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
    public function setSku($sku)
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
    public function setSlug($slug)
    {
        $this->slug = self::slugify(trim($slug));
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
    public function setName($name)
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
    public function setDescription($description)
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
    public function setRawDescription($rawDescription)
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
    public function setDescriptionFormatter($descriptionFormatter)
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
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * {@inheritdoc}
     */
    public function setVat($vat)
    {
        $this->vat = $vat;
    }

    /**
     * {@inheritdoc}
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * {@inheritdoc}
     */
    public function setStock($stock)
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
    public function setEnabled($enabled)
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
    public function setUpdatedAt(\DateTime $updatedAt = null)
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
    public function setCreatedAt(\DateTime $createdAt = null)
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
    public function addPackage(PackageInterface $package)
    {
        $package->setProduct($this);

        $this->packages->add($package);
    }

    /**
     * {@inheritdoc}
     */
    public function removePackage(PackageInterface $package)
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
    public function setPackages(ArrayCollection $packages)
    {
        $this->packages = $packages;
    }

    /**
     * {@inheritdoc}
     */
    public function addDelivery(DeliveryInterface $delivery)
    {
        $delivery->setProduct($this);

        $this->deliveries->add($delivery);
    }

    /**
     * {@inheritdoc}
     */
    public function removeDelivery(DeliveryInterface $delivery)
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
    public function setDeliveries(ArrayCollection $deliveries)
    {
        $this->deliveries = $deliveries;
    }

    /**
     * {@inheritdoc}
     */
    public function addDeliverie(DeliveryInterface $delivery)
    {
        $this->addDelivery($delivery);
    }

    /**
     * {@inheritdoc}
     */
    public function removeDeliverie(DeliveryInterface $delivery)
    {
        $this->removeDelivery($delivery);
    }

    /**
     * {@inheritdoc}
     */
    public function addProductCategorie(ProductCategoryInterface $productCategory)
    {
        $this->addProductCategory($productCategory);
    }

    /**
     * {@inheritdoc}
     */
    public function removeProductCategorie(ProductCategoryInterface $productCategory)
    {
        $this->removeProductCategory($productCategory);
    }

    /**
     * {@inheritdoc}
     */
    public function addProductCategory(ProductCategoryInterface $productCategory)
    {
        $productCategory->setProduct($this);

        $this->productCategories->add($productCategory);
    }

    /**
     * {@inheritdoc}
     */
    public function removeProductCategory(ProductCategoryInterface $productCategory)
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
    public function setProductCategories(ArrayCollection $productCategories)
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
    public function addVariation(ProductInterface $variation)
    {
        $variation->setParent($this);

        $this->variations->add($variation);
    }

    /**
     * {@inheritdoc}
     */
    public function removeVariation(ProductInterface $variation)
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
    public function setVariations(ArrayCollection $variations)
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
    public function setImage(MediaInterface $image = null)
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
    public function setGallery(GalleryInterface $gallery = null)
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
    public function setParent(ProductInterface $parent)
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
    public function setOptions(array $options)
    {
        $this->options = $options;
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

    public function preUpdate()
    {
        $this->updatedAt = new \DateTime;
    }

    public function prePersist()
    {
        $this->createdAt = new \DateTime;
        $this->updatedAt = new \DateTime;
    }

    /**
     * source : http://snipplr.com/view/22741/slugify-a-string-in-php/
     *
     * @static
     * @param  $text
     * @return mixed|string
     */
    public static function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        if (function_exists('iconv')) {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array(
            'sku'                  => $this->sku,
            'slug'                 => $this->slug,
            'name'                 => $this->name,
            'description'          => $this->description,
            'rawDescription'       => $this->rawDescription,
            'descriptionFormatter' => $this->descriptionFormatter,
            'price'                => $this->price,
            'vat'                  => $this->vat,
            'stock'                => $this->stock,
            'enabled'              => $this->enabled,
            'options'              => $this->options,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function fromArray($array)
    {
        if (array_key_exists('sku', $array)) {
            $this->sku = $array['sku'];
        }

        if (array_key_exists('slug', $array)) {
            $this->slug = $array['slug'];
        }

        if (array_key_exists('name', $array)) {
            $this->name = $array['name'];
        }

        if (array_key_exists('description', $array)) {
            $this->description = $array['description'];
        }

        if (array_key_exists('rawDescription', $array)) {
            $this->rawDescription = $array['rawDescription'];
        }

        if (array_key_exists('descriptionFormatter', $array)) {
            $this->descriptionFormatter = $array['descriptionFormatter'];
        }

        if (array_key_exists('price', $array)) {
            $this->price = $array['price'];
        }

        if (array_key_exists('vat', $array)) {
            $this->vat = $array['vat'];
        }

        if (array_key_exists('stock', $array)) {
            $this->stock = $array['stock'];
        }

        if (array_key_exists('enabled', $array)) {
            $this->enabled = $array['enabled'];
        }

        if (array_key_exists('options', $array)) {
            $this->options = $array['options'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasOneMainCategory()
    {
        if ($this->getCategories()->count() == 0) {
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
    public function validateOneMainCategory(ExecutionContext $context)
    {
        if ($this->getCategories()->count() == 0) {
            return;
        }

        if (!$this->hasOneMainCategory()) {
            $context->addViolation('sonata.product.must_have_one_main_category');
        }
    }
}
