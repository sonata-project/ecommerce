<?php

namespace Sonata\Component\Basket;

use Doctrine\ORM\EntityManager;
use Sonata\Component\Customer\CustomerInterface;
use Doctrine\ORM\NoResultException;

class BasketManager implements BasketManagerInterface
{
    protected $em;
    protected $class;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param string $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em    = $em;
        $this->class = $class;
    }

    /**
     * Creates an empty basket instance
     *
     * @return \Sonata\Component\Basket\BasketInterface
     */
    public function create()
    {
        $class = $this->class;

        return new $class;
    }

    /**
     * Updates a basket
     *
     * @param \Sonata\Component\Basket\BasketInterface $basket
     * @return void
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
     * Finds one basket by the given criteria
     *
     * @param array $criteria
     * @return \Sonata\Component\Basket\BasketInterface
     */
    public function findOneBy(array $criteria)
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    /**
     * Returns the basket's fully qualified class name
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Finds many baskets by the given criteria
     *
     * @param array $criteria
     * @return \Sonata\Component\Basket\BasketInterface[]
     */
    public function findBy(array $criteria)
    {
        return $this->getRepository()->findBy($criteria);
    }

    /**
     * Deletes a basket
     *
     * @param \Sonata\Component\Basket\BasketInterface $basket
     * @return void
     */
    public function delete(BasketInterface $basket)
    {
        $this->em->remove($basket);
        $this->em->flush();
    }

    /**
     * @param \Sonata\Component\Customer\CustomerInterface $customer
     * @return mixed|null
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
        } catch(NoResultException $e) {
            return null;
        }
    }
}
