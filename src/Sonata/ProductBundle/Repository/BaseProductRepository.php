<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * @author Sylvain Deloux <sylvain.deloux@fullsix.com>
 */
class BaseProductRepository extends EntityRepository
{
    /**
     * Returns an array of last products.
     *
     * @param int $limit Number max of required results
     *
     * @return array
     */
    public function findLastActiveProducts($limit = 5)
    {
        $rootEnabledProducts = $this->createQueryBuilder('pr')
            ->select('pr.id')
            ->where('pr.parent is null and pr.enabled = :enabled');
        $enabledChildren = $this->createQueryBuilder('pc')
            ->select('pc.id')
            ->innerJoin('pc.parent', 'pa')
            ->where('pa.enabled = :enabled');

        $eb = $this->getEntityManager()->getExpressionBuilder();

        return $this->createQueryBuilder('p')
            ->select('p', 'i')
            ->leftJoin('p.image', 'i')
            ->where(
                $eb->orX(
                    $eb->in('p.id', $rootEnabledProducts->getDQL()),
                    $eb->in('p.id', $enabledChildren->getDQL())
                )
            )
            ->andWhere('p.price is not null and p.price != 0')
            ->andWhere('p.stock is not null and p.stock != 0')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setParameters(array(
                'enabled' => true
            ))
            ->getQuery()
            ->execute();
    }
}
