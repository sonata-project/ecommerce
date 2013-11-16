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
use Sonata\UserBundle\Model\UserInterface;

class OrderManager implements OrderManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;
    /**
     * @var EntityRepository
     */
    protected $repository;
    /**
     * @var string
     */
    protected $class;

    /**
     * @param EntityManager $em
     * @param string        $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em    = $em;
        $this->class = $class;

        if (class_exists($class)) {
            $this->repository = $this->em->getRepository($class);
        }
    }

    /**
     * Creates an empty order instance
     *
     * @return Order
     */
    public function create()
    {
        $class = $this->class;

        return new $class;
    }

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
     * Returns the order's fully qualified class name
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Finds one order by the given criteria
     *
     * @param  array $criteria
     * @return Order
     */
    public function findOneBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
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

    /**
     * Deletes a order
     *
     * @param  OrderInterface $order
     * @return void
     */
    public function delete(OrderInterface $order)
    {
        $this->em->remove($order);
        $this->em->flush();
    }
}
