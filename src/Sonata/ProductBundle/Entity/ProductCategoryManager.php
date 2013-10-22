<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sonata\ProductBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\Component\Product\ProductCategoryManagerInterface;
use Sonata\Component\Product\ProductCategoryInterface;
use Doctrine\ORM\EntityManager;
use Sonata\Component\Product\ProductInterface;

class ProductCategoryManager implements ProductCategoryManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;
    /**
     * @var EntityRepository
     */
    protected $repository;
    /**
     * @var string
     */
    protected $class;

    /**
     * @param EntityManager $em
     * @param string        $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em    = $em;
        $this->class = $class;

        if (class_exists($class)) {
            $this->repository = $this->em->getRepository($class);
        }
    }

    /**
     * Creates an empty productCategory instance
     *
     * @return ProductCategoryInterface
     */
    public function createProductCategory()
    {
        $class = $this->class;

        return new $class;
    }

    /**
     * Updates a productCategory
     *
     * @param ProductCategoryInterface $productCategory
     *
     * @return void
     */
    public function updateProductCategory(ProductCategoryInterface $productCategory)
    {
        $this->em->persist($productCategory);
        $this->em->flush();
    }

    /**
     * Returns the productCategory's fully qualified class name
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Finds one productCategory by the given criteria
     *
     * @param  array $criteria
     *
     * @return ProductCategoryInterface
     */
    public function findProductCategoryBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * Deletes an productCategory
     *
     * @param ProductCategoryInterface $productCategory
     *
     * @return void
     */
    public function deleteProductCategory(ProductCategoryInterface $productCategory)
    {
        $this->em->remove($productCategory);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function addCategoryToProduct(ProductInterface $product, CategoryInterface $category)
    {
        if ($this->findProductCategoryBy(array('category' => $category, 'product' => $product))) {
            return;
        }

        $productCategory = $this->createProductCategory();

        $productCategory->setProduct($product);
        $productCategory->setCategory($category);

        $product->addProductCategory($productCategory);

        $this->updateProductCategory($productCategory);
    }

    /**
     * {@inheritdoc}
     */
    public function removeCategoryFromProduct(ProductInterface $product, CategoryInterface $category)
    {
        if (!$productCategory = $this->findProductCategoryBy(array('category' => $category, 'product' => $product))) {
            return;
        }

        $product->removeProductCategory($productCategory);

        $this->deleteProductCategory($productCategory);
    }
}
