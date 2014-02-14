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
use Sonata\CoreBundle\Model\BaseEntityManager;

class ProductCollectionManager extends BaseEntityManager implements ProductCollectionManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function addCollectionToProduct(ProductInterface $product, CollectionInterface $collection)
    {
        if ($this->findOneBy(array('collection' => $collection, 'product' => $product))) {
            return;
        }

        $productCollection = $this->create();

        $productCollection->setProduct($product);
        $productCollection->setCollection($collection);
        $productCollection->setEnabled(true);

        $product->addProductCollection($productCollection);

        $this->save($productCollection);
    }

    /**
     * {@inheritdoc}
     */
    public function removeCollectionFromProduct(ProductInterface $product, CollectionInterface $collection)
    {
        if (!$productCollection = $this->findOneBy(array('collection' => $collection, 'product' => $product))) {
            return;
        }

        $product->removeProductCollection($productCollection);

        $this->delete($productCollection);
    }
}
