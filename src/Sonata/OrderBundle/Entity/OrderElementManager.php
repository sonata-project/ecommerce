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

use Sonata\Component\Order\OrderElementManagerInterface;
use Sonata\Component\Order\OrderElementInterface;
use Doctrine\ORM\EntityManager;

class OrderElementManager implements OrderElementManagerInterface
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
     * Creates an empty orderElement instance
     *
     * @return OrderElement
     */
    public function create()
    {
        $class = $this->class;

        return new $class;
    }

    /**
     * Updates a orderElement
     *
     * @param  OrderElement $orderElement
     * @return void
     */
    public function save(OrderElementInterface $orderElement)
    {
        $this->em->persist($orderElement);
        $this->em->flush();
    }

    /**
     * Returns the orderElement's fully qualified class name
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Finds one orderElement by the given criteria
     *
     * @param  array        $criteria
     * @return OrderElement
     */
    public function findOneBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * Finds many OrderElement by the given criteria
     *
     * @param  array        $criteria
     * @return OrderElement
     */
    public function findBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }

    /**
     * Deletes an orderElement
     *
     * @param  OrderElement $orderElement
     * @return void
     */
    public function delete(OrderElementInterface $orderElement)
    {
        $this->em->remove($orderElement);
        $this->em->flush();
    }
}
