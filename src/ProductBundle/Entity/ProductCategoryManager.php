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
use Doctrine\ORM\NativeQuery;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\Component\Product\ProductCategoryManagerInterface;
use Sonata\Component\Product\ProductCategoryInterface;
use Doctrine\ORM\EntityManager;
use Sonata\Component\Product\ProductInterface;
use Sonata\CoreBundle\Model\BaseEntityManager;

class ProductCategoryManager extends BaseEntityManager implements ProductCategoryManagerInterface
{
//    const CATEGORY_PRODUCT_TYPE = 'product';

    /**
     * @var \Sonata\ClassificationBundle\Model\CategoryManagerInterface
     */
    protected $categoryManager;

    /**
     * Constructor
     *
     * @param string                   $class
     * @param EntityManager            $em
     * @param CategoryManagerInterface $categoryManager
    public function __construct($class, EntityManager $em, CategoryManagerInterface $categoryManager)
    {
        parent::__construct($class, $em);

        $this->categoryManager = $categoryManager;
    }
     */

    /**
     * {@inheritdoc}
     */
    public function addCategoryToProduct(ProductInterface $product, CategoryInterface $category, $main = false)
    {
        if ($this->findOneBy(array('category' => $category, 'product' => $product))) {
            return;
        }
//
//        if (null !== $category->getType() && self::CATEGORY_PRODUCT_TYPE !== $category->getType()) {
//            // Should we throw an exception instead?
//            $category->setType(self::CATEGORY_PRODUCT_TYPE);
//            $this->categoryManager->save($category);
//        }

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
     * {@inheritdoc}
     */
    public function getProductCount(CategoryInterface $category, $limit = 1000)
    {
        // Can't perform limit in subqueries with Doctrine... Hence raw SQL
        $metadata = $this->getEntityManager()->getClassMetadata($this->class);
        $productMetadata = $this->getEntityManager()->getClassMetadata($metadata->getAssociationTargetClass('product'));
        $categoryMetadata = $this->getEntityManager()->getClassMetadata($metadata->getAssociationTargetClass('category'));

        $sql = "SELECT count(cnt.product_id) AS cntId
            FROM (
                SELECT DISTINCT pc.product_id
                FROM %s pc
                LEFT JOIN %s p ON pc.product_id = p.id
                LEFT JOIN %s c ON pc.category_id = c.id
                LEFT JOIN %s p2 ON p.id = p2.parent_id
                WHERE p.enabled = :enabled
                AND (p2.enabled = :enabled OR p2.enabled IS NULL)
                AND (c.enabled = :enabled OR c.enabled IS NULL)
                AND p.parent_id IS NULL
                AND pc.category_id = :categoryId
                LIMIT %d
                ) AS cnt";

        $sql = sprintf($sql, $metadata->table['name'], $productMetadata->table['name'], $categoryMetadata->table['name'], $productMetadata->table['name'], $limit);

        $statement = $this->getConnection()->prepare($sql);
        $statement->bindValue('enabled', 1);
        $statement->bindValue('categoryId', $category->getId());

        $statement->execute();
        $res = $statement->fetchAll();

        return $res[0]['cntId'];
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