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
use FOS\UserBundle\Model\UserInterface;

class CustomerManager implements CustomerManagerInterface
{
    protected $em;
    protected $repository;
    protected $class;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em    = $em;
        $this->class = $class;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository($this->class);
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
     * @param \Sonata\Component\Customer\CustomerInterface $customer
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
     * @return CustomerInterface
     */
    public function findOneBy(array $criteria)
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    /**
     * Finds many customers by the given criteria
     *
     * @param array $criteria
     * @return CustomerInterface[]
     */
    public function findBy(array $criteria)
    {
        return $this->getRepository()->findBy($criteria);
    }

    /**
     * Deletes a customer
     *
     * @param \Sonata\Component\Customer\CustomerInterface $customer
     * @return void
     */
    public function delete(CustomerInterface $customer)
    {
        $this->em->remove($customer);
        $this->em->flush();
    }

    /**
     * Returns the main customer linked to the user, created it if done
     *
     * @param \FOS\UserBundle\Model\UserInterface $user
     * @return Customer|CustomerInterface
     */
    public function getMainCustomer(UserInterface $user)
    {
        $customer = $this->findOneBy(array(
            'user' => $user->getId()
        ));

        if (!$customer) {
            $customer = $this->create();
            $customer->setUser($user);
            $customer->setEmail($user->getEmail());
            $customer->setIsComplete(false);

            $this->save($customer);
        }

        return $customer;
    }
}