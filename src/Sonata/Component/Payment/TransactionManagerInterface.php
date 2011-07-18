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
     * @return Transaction
     */
    function create();

    /**
     * Deletes a transaction
     *
     * @param transaction * $transaction
     * @return void
     */
    function delete(TransactionInterface $transaction);

    /**
     * Finds one transaction by the given criteria
     *
     * @param array $criteria
     * @return TransactionInterface
     */
    function findOneBy(array $criteria);

    /**
     * Finds many transaction by the given criteria
     *
     * @param array $criteria
     * @return TransactionInterface
     */
    function findBy(array $criteria);

    /**
     * Returns the transaction's fully qualified class name
     *
     * @return string
     */
    function getClass();

    /**
     * Updates a transaction
     *
     * @param Transaction $transaction
     * @return void
     */
    function save(TransactionInterface $transaction);
}