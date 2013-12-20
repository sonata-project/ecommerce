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
use Sonata\CoreBundle\Entity\DoctrineBaseManager;

class ProductCategoryManager extends DoctrineBaseManager implements ProductCategoryManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function addCategoryToProduct(ProductInterface $product, CategoryInterface $category, $main = false)
    {
        if ($this->findOneBy(array('category' => $category, 'product' => $product))) {
            return;
        }

        $productCategory = $this->create();

        $productCategory->setProduct($product);
        $productCategory->setCategory($category);
        $productCategory->setEnabled(true);
        $productCategory->setMain($main);

        $product->addProductCategory($productCategory);

        $this->save($productCategory);
    }

    /**
     * {@inheritdoc}
     */
    public function removeCategoryFromProduct(ProductInterface $product, CategoryInterface $category)
    {
        if (!$productCategory = $this->findOneBy(array('category' => $category, 'product' => $product))) {
            return;
        }

        $product->removeProductCategory($productCategory);

        $this->delete($productCategory);
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryTree()
    {
        $qb = $this->getRepository()->createQueryBuilder('pc')
            ->select('c, pc')
            ->leftJoin('pc.category', 'c')
            ->where('pc.enabled = true')
            ->andWhere('c.enabled = true')
            ->groupBy('c.id')
        ;

        $pCategories = $qb->getQuery()->execute();

        $categoryTree = array();

        foreach ($pCategories as $category) {
            $this->putInTree($category->getCategory(), $categoryTree);
        }

        return $categoryTree;
    }

    /**
     * Finds $category place in $tree
     *
     * @param CategoryInterface $category
     * @param array             $tree
     */
    protected function putInTree(CategoryInterface $category, array &$tree)
    {
        if (null === $category->getParent()) {
            $tree[$category->getId()] = $category;
        } else {
            $this->putInTree($category->getParent(), $tree);
        }
    }


}
