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
    public function findLastProducts($limit = 5)
    {
        return $this->createQueryBuilder('p')
            ->where('p.enabled = :enabled')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setParameters(array(
                'enabled' => true
            ))
            ->getQuery()
            ->execute();
    }
}
