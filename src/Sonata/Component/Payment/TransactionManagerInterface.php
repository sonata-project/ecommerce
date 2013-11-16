<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Payment;

interface TransactionManagerInterface
{
    /**
     * Creates an empty transaction instance
     *
     * @return TransactionInterface
     */
    public function create();

    /**
     * Deletes a transaction
     *
     * @param  TransactionInterface $transaction
     * @return void
     */
    public function delete(TransactionInterface $transaction);

    /**
     * Finds one transaction by the given criteria
     *
     * @param  array                $criteria
     * @return TransactionInterface
     */
    public function findOneBy(array $criteria);

    /**
     * Finds many transaction by the given criteria
     *
     * @param  array                $criteria
     * @return TransactionInterface
     */
    public function findBy(array $criteria);

    /**
     * Returns the transaction's fully qualified class name
     *
     * @return string
     */
    public function getClass();

    /**
     * Updates a transaction
     *
     * @param  TransactionInterface $transaction
     * @return void
     */
    public function save(TransactionInterface $transaction);
}
