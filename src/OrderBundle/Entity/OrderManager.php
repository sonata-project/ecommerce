<?php

/*
 * This file is part of the Sonata Project package.
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
    public function findForUser(UserInterface $user)
    {
        $qb = $this->getRepository()->createQueryBuilder('o')
            ->leftJoin('o.customer', 'c')
            ->where('c.user = :user')
            ->orderBy('o.createdAt', 'DESC')
            ->setParameter('user', $user);

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
}
