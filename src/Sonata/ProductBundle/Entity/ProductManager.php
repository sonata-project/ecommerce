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

use Sonata\AdminBundle\Datagrid\PagerInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\ProductManagerInterface;
use Sonata\CoreBundle\Entity\DoctrineBaseManager;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\DoctrineORMAdminBundle\Datagrid\Pager;

class ProductManager extends DoctrineBaseManager implements ProductManagerInterface
{
    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * {@inheritdoc}
     */
    public function findInSameCollections($productCollections, $limit = null)
    {
        return $this->queryInSameCollections($productCollections, $limit)
            ->getQuery()
            ->execute();
    }

    /**
     * {@inheritdoc}
     */
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
     * @param int $categoryId
     * @param int $page
     * @param int $limit
     *
     * @return PagerInterface
     */
    public function getProductsByCategoryIdPager($categoryId, $page = 1, $limit = 25)
    {
        $queryBuilder = $this->queryProductsByCategoryIdPager($categoryId);

        $pager = new Pager($limit);
        $pager->setQuery(new ProxyQuery($queryBuilder));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }

    /**
     * @param int $categoryId
     * @param int $page
     * @param int $limit
     *
     * @return Pager
     */
    public function getActiveProductsByCategoryIdPager($categoryId, $page = 1, $limit = 25)
    {
        $queryBuilder = $this->queryProductsByCategoryIdPager($categoryId);
        $queryBuilder->andWhere('p.enabled = :enabled')
            ->setParameter('enabled', true);

        $pager = new Pager($limit);
        $pager->setQuery(new ProxyQuery($queryBuilder));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }

    /**
     * Retrieve an active product from its id and its slug
     *
     * @param int    $id
     * @param string $slug
     *
     * @return ProductInterface|null
     */
    public function findEnabledFromIdAndSlug($id, $slug)
    {
        return $this->em
            ->getRepository($this->getClass())
            ->findOneBy(array(
                'id' => $id,
                'slug' => $slug,
                'enabled' => true
            ));
    }

    /**
     * @param int $categoryId
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function queryProductsByCategoryIdPager($categoryId)
    {
        return $this->em
            ->createQueryBuilder('p')
            ->from($this->getClass(), 'p')
            ->select('p')
            ->leftJoin('p.productCategories', 'pc')
            ->leftJoin('p.gallery', 'g')
            ->where('pc.category = :categoryId')
            ->setParameter('categoryId', $categoryId);
    }

    /**
     * @param array $productCollections
     * @param null|int $limit
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function queryInSameCollections($productCollections, $limit = null)
    {
        $collections = array();
        $productIds  = array();

        foreach ($productCollections as $pCollection) {
            $collections[] = $pCollection->getCollection();
            if (false === array_search($pCollection->getProduct()->getId(), $productIds)) {
                $productIds[] = $pCollection->getProduct()->getId();
            }
        }

        $queryBuilder = $this->em->createQueryBuilder('p')
            ->select('p')
            ->distinct()
            ->from($this->getClass(), 'p')
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
