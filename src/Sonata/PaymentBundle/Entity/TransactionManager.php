<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sonata\TransactionBundle\Entity;

use Sonata\Component\Payment\TransactionManagerInterface;
use Sonata\Component\Payment\TransactionInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class TransactionManager implements TransactionManagerInterface
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
     * Creates an empty medie instance
     *
     * @return Transaction
     */
    public function createTransaction()
    {
        $class = $this->class;

        return new $class;
    }

    /**
     * Updates a transaction
     *
     * @param Transaction $transaction
     * @return void
     */
    public function updateTransaction(TransactionInterface $transaction)
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
     * @param array $criteria
     * @return Transaction
     */
    public function findTransactionBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * Deletes a transaction
     *
     * @param Transaction $transaction
     * @return void
     */
    public function deleteTransaction(TransactionInterface $transaction)
    {
        $this->em->remove($transaction);
        $this->em->flush();
    }
}