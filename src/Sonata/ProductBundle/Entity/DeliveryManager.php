<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sonata\ProductBundle\Entity;

use Sonata\Component\Product\DeliveryManagerInterface;
use Sonata\Component\Product\DeliveryInterface;
use Doctrine\ORM\EntityManager;

class DeliveryManager implements DeliveryManagerInterface
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
     * Creates an empty delivery instance
     *
     * @return delivery
     */
    public function createDelivery()
    {
        $class = $this->class;

        return new $class;
    }

    /**
     * Updates a delivery
     *
     * @param \Sonata\Component\Product\DeliveryInterface $delivery
     *
     * @return void
     */
    public function updateDelivery(DeliveryInterface $delivery)
    {
        $this->em->persist($delivery);
        $this->em->flush();
    }

    /**
     * Returns the delivery's fully qualified class name
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Finds one delivery by the given criteria
     *
     * @param  array    $criteria
     * @return Delivery
     */
    public function findDeliveryBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * Deletes a delivery
     *
     * @param \Sonata\Component\Product\DeliveryInterface $delivery
     *
     * @return void
     */
    public function deleteDelivery(DeliveryInterface $delivery)
    {
        $this->em->remove($delivery);
        $this->em->flush();
    }
}
