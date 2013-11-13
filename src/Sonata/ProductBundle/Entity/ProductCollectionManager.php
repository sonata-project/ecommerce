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
use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\Component\Product\ProductCollectionManagerInterface;
use Sonata\Component\Product\ProductCollectionInterface;
use Doctrine\ORM\EntityManager;
use Sonata\Component\Product\ProductInterface;

class ProductCollectionManager implements ProductCollectionManagerInterface
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
     * Creates an empty productCollection instance
     *
     * @return ProductCollectionInterface
     */
    public function createProductCollection()
    {
        $class = $this->class;

        return new $class;
    }

    /**
     * Updates a productCollection
     *
     * @param ProductCollectionInterface $productCollection
     *
     * @return void
     */
    public function updateProductCollection(ProductCollectionInterface $productCollection)
    {
        $this->em->persist($productCollection);
        $this->em->flush();
    }

    /**
     * Returns the productCollection's fully qualified class name
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Finds one productCollection by the given criteria
     *
     * @param array $criteria
     *
     * @return ProductCollectionInterface
     */
    public function findProductCollectionBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * Deletes an productCollection
     *
     * @param ProductCollectionInterface $productCollection
     *
     * @return void
     */
    public function deleteProductCollection(ProductCollectionInterface $productCollection)
    {
        $this->em->remove($productCollection);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function addCollectionToProduct(ProductInterface $product, CollectionInterface $collection)
    {
        if ($this->findProductCollectionBy(array('collection' => $collection, 'product' => $product))) {
            return;
        }

        $productCollection = $this->createProductCollection();

        $productCollection->setProduct($product);
        $productCollection->setCollection($collection);
        $productCollection->setEnabled(true);

        $product->addProductCollection($productCollection);

        $this->updateProductCollection($productCollection);
    }

    /**
     * {@inheritdoc}
     */
    public function removeCollectionFromProduct(ProductInterface $product, CollectionInterface $collection)
    {
        if (!$productCollection = $this->findProductCollectionBy(array('collection' => $collection, 'product' => $product))) {
            return;
        }

        $product->removeProductCollection($productCollection);

        $this->deleteProductCollection($productCollection);
    }
}
