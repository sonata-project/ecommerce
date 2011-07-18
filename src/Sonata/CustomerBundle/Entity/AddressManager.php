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

use Sonata\Component\Customer\AddressManagerInterface;
use Sonata\Component\Customer\AddressInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class AddressManager implements AddressManagerInterface
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
     * Creates an empty Address instance
     *
     * @return Address
     */
    public function create()
    {
        $class = $this->class;

        return new $class;
    }

    /**
     * Updates an address
     *
     * @param Address $address
     * @return void
     */
    public function save(AddressInterface $address)
    {
        $this->em->persist($address);
        $this->em->flush();
    }

    /**
     * Returns the address's fully qualified class name
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Finds one address by the given criteria
     *
     * @param array $criteria
     * @return Address
     */
    public function findBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }

    public function findOneBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * Deletes an address
     *
     * @param Address $address
     * @return void
     */
    public function delete(AddressInterface $address)
    {
        $this->em->remove($address);
        $this->em->flush();
    }
}