<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sonata\PaymentBundle\Entity;

use Sonata\Component\Payment\TransactionManagerInterface;
use Sonata\Component\Payment\TransactionInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class TransactionManager implements TransactionManagerInterface
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
    }

    /**
     * Creates an empty Transaction instance
     *
     * @return Transaction
     */
    public function create()
    {
        $class = $this->class;

        return new $class;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository($this->class);
    }

    /**
     * Updates a transaction
     *
     * @param  \Sonata\Component\Payment\TransactionInterface $transaction
     * @return void
     */
    public function save(TransactionInterface $transaction)
    {
        $this->em->persist($transaction);
        $this->em->flush();
    }

    /**
     * Returns the transaction's fully qualified class name
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Finds one transaction by the given criteria
     *
     * @param  array       $criteria
     * @return Transaction
     */
    public function findOneBy(array $criteria)
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    /**
     * Finds many transaction by the given criteria
     *
     * @param  array       $criteria
     * @return Transaction
     */
    public function findBy(array $criteria)
    {
        return $this->getRepository()->findBy($criteria);
    }

    /**
     * Deletes a transaction
     *
     * @param  \Sonata\Component\Payment\TransactionInterface $transaction
     * @return void
     */
    public function delete(TransactionInterface $transaction)
    {
        $this->em->remove($transaction);
        $this->em->flush();
    }
}
