<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sonata\OrderBundle\Entity;

use Sonata\Component\Order\OrderManagerInterface;
use Sonata\CoreBundle\Model\BaseEntityManager;
use Sonata\UserBundle\Model\UserInterface;
use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;

class OrderManager extends BaseEntityManager implements OrderManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function save($order, $andFlush = true)
    {
        $this->getEntityManager()->persist($order->getCustomer());

        parent::save($order, $andFlush);
    }

    /**
     * {@inheritdoc}
     */
    public function findForUser(UserInterface $user, array $orderBy = array(), $limit = null, $offset = null)
    {
        $qb = $this->getRepository()->createQueryBuilder('o')
            ->leftJoin('o.customer', 'c')
            ->where('c.user = :user')
            ->setParameter('user', $user);

        foreach ($orderBy as $field => $dir) {
            $qb->orderBy('o.'.$field, $dir);
        }

        if (null !== $limit) {
            $qb->setMaxResults($limit);
        }

        if (null !== $offset) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder($orderId)
    {
        $qb = $this->getRepository()->createQueryBuilder('o')
            ->select('o')
            ->innerJoin('o.orderElements', 'oe')
            ->where('o.id = :id')
            ->setParameter('id', $orderId);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getPager(array $criteria, $page, $limit = 10, array $sort = array())
    {
        $query = $this->getRepository()
            ->createQueryBuilder('o')
            ->select('o');

        $fields = $this->getEntityManager()->getClassMetadata($this->class)->getFieldNames();
        foreach ($sort as $field => $direction) {
            if (!in_array($field, $fields)) {
                throw new \RuntimeException(sprintf("Invalid sort field '%s' in '%s' class", $field, $this->class));
            }
        }
        if (count($sort) == 0) {
            $sort = array('reference' => 'ASC');
        }
        foreach ($sort as $field => $direction) {
            $query->orderBy(sprintf('o.%s', $field), strtoupper($direction));
        }

        $parameters = array();

        if (isset($criteria['status'])) {
            $query->andWhere('o.status = :status');
            $parameters['status'] = $criteria['status'];
        }

        if (isset($criteria['customer'])) {
            $query->innerJoin('o.customer', 'c', 'WITH', 'c.id = :customer');
            $parameters['customer'] = $criteria['customer'];
        }

        $query->setParameters($parameters);

        $pager = new Pager();
        $pager->setMaxPerPage($limit);
        $pager->setQuery(new ProxyQuery($query));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }
}
