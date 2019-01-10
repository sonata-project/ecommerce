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

namespace Sonata\ProductBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
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
        return $this->createQueryBuilder('p')
            ->select('p', 'i', 'g')
            ->distinct()
            ->leftJoin('p.image', 'i')
            ->leftJoin('p.gallery', 'g')
            ->leftJoin('p.variations', 'pv')
            ->andWhere('p.parent IS NULL')      // Limit to master products or products without variations
            ->andWhere('p.enabled = :enabled')
            ->andWhere('pv.enabled = :enabled or pv.enabled IS NULL')
            ->andWhere('p.stock != 0')
            ->andWhere('p.price != 0')
            ->andWhere('pv.stock != 0 or pv.stock IS NULL')
            ->andWhere('pv.price != 0 or pv.price IS NULL')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setParameters([
                'enabled' => true,
            ])
            ->getQuery()
            ->execute();
    }
}
