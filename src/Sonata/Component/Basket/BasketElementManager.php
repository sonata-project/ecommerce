<?php

namespace Sonata\Component\Basket;

use Doctrine\ORM\EntityManager;

class BasketElementManager implements BasketElementManagerInterface
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
     * Creates an empty basket element instance
     *
     * @return \Sonata\Component\Basket\BasketElementInterface
     */
    public function create()
    {
        $class = $this->class;

        return new $class;
    }

    /**
     * Updates a basket element
     *
     * @param \Sonata\Component\Basket\BasketElementInterface $basketElement
     * @return void
     */
    public function save(BasketElementInterface $basketElement)
    {
        $this->em->persist($basketElement);
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
     * @return \Sonata\Component\Basket\BasketElementInterface
     */
    public function findOneBy(array $criteria)
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    /**
     * Returns the basket element's fully qualified class name
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Finds many basket elements by the given criteria
     *
     * @param array $criteria
     * @return \Sonata\Component\Basket\BasketElementInterface[]
     */
    public function findBy(array $criteria)
    {
        return $this->getRepository()->findBy($criteria);
    }

    /**
     * Deletes a basket
     *
     * @param \Sonata\Component\Basket\BasketElementInterface $basketElement
     * @return void
     */
    public function delete(BasketElementInterface $basketElement)
    {
        $this->em->remove($basketElement);
        $this->em->flush();
    }
}
