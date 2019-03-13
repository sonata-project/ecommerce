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

use Doctrine\ORM\QueryBuilder;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\ProductManagerInterface;
use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;
use Sonata\Doctrine\Entity\BaseEntityManager;

class ProductManager extends BaseEntityManager implements ProductManagerInterface
{
    public function findInSameCollections($productCollections, $limit = null)
    {
        return $this->queryInSameCollections($productCollections, $limit)
            ->getQuery()
            ->execute();
    }

    public function findParentsInSameCollections($productCollections, $limit = null)
    {
        return $this->queryInSameCollections($productCollections, $limit)
            ->andWhere('p.parent IS NULL')
            ->andWhere('p.enabled = :enabled')
            ->setParameter('enabled', true)
            ->getQuery()
            ->execute();
    }

    /**
     * Returns partial product example (only to get its class) from $category.
     *
     * @param CategoryInterface $category
     *
     * @return ProductInterface|null
     */
    public function findProductForCategory(CategoryInterface $category)
    {
        return $this->getCategoryProductsQueryBuilder($category)
            ->select('partial p.{id}')
            ->setMaxResults(1)
        ->getQuery()->getOneOrNullResult();
    }

    /**
     * Returns active products for a given category.
     *
     * @param CategoryInterface $category
     * @param string            $filter
     * @param mixed             $option
     *
     * @return QueryBuilder
     */
    public function getCategoryActiveProductsQueryBuilder(CategoryInterface $category = null, $filter = null, $option = null)
    {
        $queryBuilder = $this->getCategoryProductsQueryBuilder($category);
        $queryBuilder->leftJoin('p.variations', 'pv')
            ->andWhere('p.parent IS NULL')      // Limit to master products or products without variations
            ->andWhere('p.enabled = :enabled')
            ->andWhere($queryBuilder->expr()->orX('pv.enabled = :enabled', 'pv.enabled IS NULL'))
            ->setParameter('enabled', true);

        if (null !== $filter) {
            // TODO manage various filter types
            $queryBuilder->andWhere(sprintf('p.%s %s :%s', $filter, '>', $filter))
                ->setParameter(sprintf(':%s', $filter), $option);
        }

        return $queryBuilder;
    }

    /**
     * Retrieve an active product from its id and its slug.
     *
     * @param int    $id
     * @param string $slug
     *
     * @return ProductInterface|null
     */
    public function findEnabledFromIdAndSlug($id, $slug)
    {
        return $this->getRepository()
            ->findOneBy([
                'id' => $id,
                'slug' => $slug,
                'enabled' => true,
            ]);
    }

    public function findVariations(ProductInterface $product)
    {
        $queryBuilder = $this->getRepository()->createQueryBuilder('p')
            ->where('p.parent = :parent')
            ->andWhere('p.enabled = :enabled')
            ->setParameter('parent', $product)
            ->setParameter('enabled', true)
        ;

        return $queryBuilder->getQuery()->execute();
    }

    public function updateStock($product, $diff): void
    {
        if (0 === $diff) {
            return;
        }

        $productId = $product instanceof ProductInterface ? $product->getId() : $product;

        $tableName = $this->getTableName();

        $operator = $diff > 0 ? '+' : '-';

        $this->getConnection()->query(sprintf('UPDATE %s SET stock = stock %s %d WHERE id = %d;', $tableName, $operator, abs($diff), $productId));
    }

    public function getPager(array $criteria, $page, $limit = 10, array $sort = [])
    {
        $query = $this->getRepository()
            ->createQueryBuilder('p')
            ->select('p');

        $fields = $this->getEntityManager()->getClassMetadata($this->class)->getFieldNames();
        foreach ($sort as $field => $direction) {
            if (!\in_array($field, $fields, true)) {
                unset($sort[$field]);
            }
        }
        if (0 === \count($sort)) {
            $sort = ['name' => 'ASC'];
        }
        foreach ($sort as $field => $direction) {
            $query->orderBy(sprintf('p.%s', $field), strtoupper($direction));
        }

        $parameters = [];

        if (isset($criteria['enabled'])) {
            $query->andWhere('p.enabled = :enabled');
            $parameters['enabled'] = $criteria['enabled'];
        }

        $query->setParameters($parameters);

        $pager = new Pager();
        $pager->setMaxPerPage($limit);
        $pager->setQuery(new ProxyQuery($query));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }

    /**
     * Returns QueryBuilder for products.
     *
     * @param CategoryInterface $category
     *
     * @return QueryBuilder
     */
    protected function getCategoryProductsQueryBuilder(CategoryInterface $category = null)
    {
        $queryBuilder = $this->getRepository()->createQueryBuilder('p')
            ->leftJoin('p.image', 'i')
            ->leftJoin('p.gallery', 'g');

        if ($category) {
            $queryBuilder
                ->leftJoin('p.productCategories', 'pc')
                ->andWhere('pc.category = :categoryId')
                ->setParameter('categoryId', $category->getId());
        }

        return $queryBuilder;
    }

    /**
     * @param array    $productCollections
     * @param int|null $limit
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function queryInSameCollections($productCollections, $limit = null)
    {
        $collections = [];
        $productIds = [];

        foreach ($productCollections as $pCollection) {
            $collections[] = $pCollection->getCollection();
            if (false === array_search($pCollection->getProduct()->getId(), $productIds, true)) {
                $productIds[] = $pCollection->getProduct()->getId();
            }
        }

        $queryBuilder = $this->getRepository()->createQueryBuilder('p')
            ->distinct()
            ->leftJoin('p.productCollections', 'pc')
            ->where('pc.collection IN (:collections)')
            ->andWhere('p.id NOT IN (:productIds)')
            ->setParameter('collections', array_values($collections))
            ->setParameter('productIds', array_values($productIds));

        if (null !== $limit) {
            $queryBuilder->setMaxResults($limit);
        }

        return $queryBuilder;
    }
}
