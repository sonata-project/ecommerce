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

use Sonata\Component\Product\ProductCategoryManagerInterface;
use Sonata\Component\Product\ProductCategoryInterface;
use Doctrine\ORM\EntityManager;

class ProductCategoryManager implements ProductCategoryManagerInterface
{
    protected $em;
    protected $repository;
    protected $class;
    
    public function __construct(EntityManager $em, $class)
    {
        $this->em    = $em;
        $this->class = $class;

        if(class_exists($class)) {
            $this->repository = $this->em->getRepository($class);
        }
    }

    /**
     * Creates an empty productCategory instance
     *
     * @return ProductCategory
     */
    public function createProductCategory()
    {
        $class = $this->class;

        return new $class;
    }

    /**
     * Updates a productCategory
     *
     * @param ProductCategory $productCategory
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
     * @param array $criteria
     * @return ProductCategory
     */
    public function findProductCategoryBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * Deletes an productCategory
     *
     * @param ProductCategory $productCategory
     * @return void
     */
    public function deleteProductCategory(ProductCategoryInterface $productCategory)
    {
        $this->em->remove($productCategory);
        $this->em->flush();
    }
}