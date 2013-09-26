<?php

namespace Sonata\Component\Basket;

use Doctrine\ORM\EntityManager;

class BasketElementManager implements BasketElementManagerInterface
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
    public function delete(BasketElementInterface $basketElement)
    {
        $this->em->remove($basketElement);
        $this->em->flush();
    }
}
