<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sonata\CustomerBundle\Entity;

use Sonata\Component\Customer\CustomerManagerInterface;
use Sonata\Component\Customer\CustomerInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class CustomerManager implements CustomerManagerInterface
{
    protected $em;
    protected $repository;
    protected $class;

    public function __construct(EntityManager $em, $class)
    {
        $this->em    = $em;
        $this->class = $class;

        if(class_exists($class)) {
            $this->repository = $this->em->getRepository($class);
        }
    }

    /**
     * Creates an empty customer instance
     *
     * @return Customer
     */
    public function create()
    {
        $class = $this->class;

        return new $class;
    }

    /**
     * Updates a customer
     *
     * @param Customer $customer
     * @return void
     */
    public function save(CustomerInterface $customer)
    {
        $this->em->persist($customer);
        $this->em->flush();
    }

    /**
     * Returns the customer's fully qualified class name
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Finds one customer by the given criteria
     *
     * @param array $criteria
     * @return Customer
     */
    public function findOneBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * Finds many customers by the given criteria
     *
     * @param array $criteria
     * @return Customer
     */
    public function findBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }

    /**
     * Deletes a customer
     *
     * @param Customer $customer
     * @return void
     */
    public function delete(CustomerInterface $customer)
    {
        $this->em->remove($customer);
        $this->em->flush();
    }
}