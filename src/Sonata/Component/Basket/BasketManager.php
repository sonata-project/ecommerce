<?php

namespace Sonata\Component\Basket;

use Doctrine\ORM\EntityManager;
use Sonata\Component\Customer\CustomerInterface;
use Doctrine\ORM\NoResultException;

class BasketManager implements BasketManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param string                      $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em    = $em;
        $this->class = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $class = $this->class;

        return new $class;
    }

    /**
     * {@inheritdoc}
     */
    public function save(BasketInterface $basket)
    {
        $this->em->persist($basket);
        $this->em->flush();
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository($this->class);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria)
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria)
    {
        return $this->getRepository()->findBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(BasketInterface $basket)
    {
        $this->em->remove($basket);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function loadBasketPerCustomer(CustomerInterface $customer)
    {
        try {
            return $this->em->createQueryBuilder()
                ->select('b, be')
                ->from($this->class, 'b')
                ->leftJoin('b.basketElements', 'be', null, null, 'be.position')
                ->where('b.customer = :customer')
                ->setParameter('customer', $customer->getId())
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
