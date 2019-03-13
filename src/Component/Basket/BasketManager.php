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

namespace Sonata\Component\Basket;

use Doctrine\ORM\NoResultException;
use Sonata\Component\Customer\CustomerInterface;
use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;
use Sonata\Doctrine\Entity\BaseEntityManager;

class BasketManager extends BaseEntityManager implements BasketManagerInterface
{
    public function loadBasketPerCustomer(CustomerInterface $customer)
    {
        try {
            return $this->getRepository()->createQueryBuilder('b')
                ->leftJoin('b.basketElements', 'be', null, null, 'be.position')
                ->where('b.customer = :customer')
                ->setParameter('customer', $customer->getId())
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            return;
        }
    }

    public function save($entity, $andFlush = true): void
    {
        foreach ($entity->getBasketElements() as $element) {
            $element->setBasket($entity);
        }

        parent::save($entity, $andFlush);
    }

    public function getPager(array $criteria, $page, $limit = 10, array $sort = [])
    {
        $query = $this->getRepository()
            ->createQueryBuilder('b')
            ->select('b');

        $fields = $this->getEntityManager()->getClassMetadata($this->class)->getFieldNames();
        foreach ($sort as $field => $direction) {
            if (!\in_array($field, $fields, true)) {
                throw new \RuntimeException(sprintf("Invalid sort field '%s' in '%s' class", $field, $this->class));
            }
        }
        if (0 === \count($sort)) {
            $sort = ['id' => 'ASC'];
        }
        foreach ($sort as $field => $direction) {
            $query->orderBy(sprintf('b.%s', $field), strtoupper($direction));
        }

        $pager = new Pager();
        $pager->setMaxPerPage($limit);
        $pager->setQuery(new ProxyQuery($query));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }
}
