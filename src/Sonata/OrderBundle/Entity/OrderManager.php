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
use Sonata\Component\Order\OrderInterface;
use Doctrine\ORM\EntityManager;
use Sonata\CoreBundle\Entity\DoctrineBaseManager;
use Sonata\UserBundle\Model\UserInterface;

class OrderManager extends DoctrineBaseManager implements OrderManagerInterface
{
    /**
     * Updates a order
     *
     * @param  OrderInterface $order
     * @return void
     */
    public function save(OrderInterface $order)
    {
        $this->em->persist($order->getCustomer());
        $this->em->persist($order);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findForUser(UserInterface $user)
    {
        $qb = $this->repository->createQueryBuilder('o')
            ->leftJoin('o.customer', 'c')
            ->where('c.user = :user')
            ->setParameter('user', $user);

        return $qb->getQuery()->execute();
    }
}
