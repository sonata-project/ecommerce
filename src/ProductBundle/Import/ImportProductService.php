<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Rascar Sylvain <sylvain.rascar@fullsix.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Import;

use Doctrine\Common\Collections\ArrayCollection;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\Component\Product\Pool;
use Sonata\Component\Product\ProductCategoryManagerInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\ProductManagerInterface;
use Sonata\CoreBundle\Model\ManagerInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class ImportProductService
 */
class ImportProductService
{
    const CREATE_STATUS = 0;

    const UPDATE_STATUS = 1;

    /**
     * @var ArrayCollection
     */
    protected $productPool;

    /**
     * @var ManagerInterface
     */
    protected $mediaManager;

    /**
     * @var ProductCategoryManagerInterface
     */
    protected $productCategoryManager;

    /**
     * @var CategoryManagerInterface
     */
    protected $categoryManager;

    /**
     * @var string
     */
    protected $productCodePrefix;

    /**
     * @var string
     */
    protected $mediaProviderKey;

    /**
     * @var string
     */
    protected $mediaContext;

    /**
     * @var array
     */
    protected $fieldMapping;

    /**
     * @param Pool                            $productPool
     * @param ManagerInterface                $mediaManager
     * @param ProductCategoryManagerInterface $productCategoryManager
     * @param CategoryManagerInterface        $categoryManager
     * @param string                          $productCodePrefix
     * @param string                          $mediaProviderKey
     * @param string                          $mediaContext
     * @param array                           $fieldMapping
     */
    public function __construct(
        Pool $productPool,
        ManagerInterface $mediaManager,
        ProductCategoryManagerInterface $productCategoryManager,
        CategoryManagerInterface $categoryManager,
        $productCodePrefix,
        $mediaProviderKey,
        $mediaContext,
        $fieldMapping
    ) {
        $this->productPool = $productPool;
        $this->mediaManager = $mediaManager;
        $this->productCategoryManager = $productCategoryManager;
        $this->categoryManager = $categoryManager;
        $this->productCodePrefix = $productCodePrefix;
        $this->mediaProviderKey = $mediaProviderKey;
        $this->mediaContext = $mediaContext;
        $this->fieldMapping = $fieldMapping;
    }

    /**
     * @param array $fields
     */
    public function setMapping(array $fields)
    {
        $this->fieldMapping = array_merge($this->fieldMapping, $fields);
    }

    /**
     * @param string $fieldName
     *
     * @return string
     */
    public function getMappedField($fieldName)
    {
        return array_key_exists($fieldName, $this->fieldMapping) ? $this->fieldMapping[$fieldName] : $fieldName;
    }

    /**
     * Insert or update a product according to the given data
     *
     * @param array $data
     * @param bool  $andFlush
     *
     * @return int
     */
    public function importProduct(array $data, $andFlush = true)
    {
        $this->checkDataValidity($data);

        /** @var ProductManagerInterface $productManager */
        $productManager = $this->getProductManager($data[$this->getMappedField('type')]);

        /** @var ProductInterface $product */
        $product = $productManager->findOneBy(array('sku' => $data[$this->getMappedField('sku')]));
        $status = self::UPDATE_STATUS;

        if (!$product) {
            $product = $productManager->create();
            $status = self::CREATE_STATUS;
        }

        foreach ($data as $column => $value) {
            if ($column !== $this->getMappedField('type')) {
                switch ($column) {
                    case $this->getMappedField('image'):
                        $media = $this->handleMedia($value, $product);
                        $product->setImage($media);
                        break;
                    case $this->getMappedField('categories'):
                        $productCategories = $this->handleCategories($value, $product);
                        $product->setProductCategories($productCategories);
                        break;
                    default:
                        $propertyAccessor = PropertyAccess::createPropertyAccessor();
                        $propertyAccessor->setValue($product, $column, $value);
                        break;
                }
            }
        }

        $productManager->save($product, $andFlush);

        return $status;
    }

    /**
     * Check column names validity
     *
     * @param array $data
     */
    protected function checkDataValidity(array $data)
    {
        $requiredFields = array('type', 'sku');

        foreach ($requiredFields as $field) {
            if (!array_key_exists($this->getMappedField($field), $data)) {
                throw new \RuntimeException(
                    sprintf(
                        'Unable to find the column with name "%s". It is required to set product %s',
                        $this->getMappedField($field),
                        $field
                    )
                );
            }
        }
    }

    /**
     * @param string $type
     *
     * @return ProductManagerInterface
     *
     * @throws \RuntimeException
     */
    protected function getProductManager($type)
    {
        $productCode = empty($this->productCodePrefix) ? $type : sprintf('%s.%s', $this->productCodePrefix, $type);
        $productManager = $this->productPool->getManager($productCode);

        if (!$productManager) {
            throw new \RuntimeException(
                sprintf('Unable to find manager for %s', $type)
            );
        }

        return $productManager;
    }

    /**
     * @param string           $imagePath
     * @param ProductInterface $product
     *
     * @return MediaInterface
     */
    protected function handleMedia($imagePath, ProductInterface $product)
    {
        $media = $product->getImage();

        if (!$media instanceof MediaInterface) {
            $media = $this->mediaManager->create();
        }

        $media->setName(basename($imagePath));
        $media->setBinaryContent($imagePath);
        $media->setEnabled(true);
        $media->setProviderName($this->mediaProviderKey);
        $media->setContext($this->mediaContext);
        $this->mediaManager->save($media);

        return $media;
    }

    /**
     * @param string           $categorySlugs
     * @param ProductInterface $product
     *
     * @return ArrayCollection
     */
    protected function handleCategories($categorySlugs, ProductInterface $product)
    {
        $oldCategories = $product->getProductCategories();

        foreach ($oldCategories as $oldCategory) {
            $this->productCategoryManager->delete($oldCategory);
        }

        $categoriesSlug = explode(',', $categorySlugs);
        $productCategories = new ArrayCollection();

        foreach ($categoriesSlug as $key => $slug) {
            /** @var CategoryInterface $category */
            if ($category = $this->categoryManager->findOneBy(array('slug' => trim($slug)))) {
                $productCategory = $this->productCategoryManager->create();
                $productCategory->setCategory($category);
                $productCategory->setProduct($product);
                $productCategory->setEnabled(true);
                $productCategory->setMain($key == 0);
                $productCategories->add($productCategory);
            }
        }

        return $productCategories;
    }
}
