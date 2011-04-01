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


interface PaymentManagerInterface
{

    /**
     * Creates an empty transaction instance
     *
     * @return Transaction
     */
    function createTransaction();

    /**
     * Deletes a transaction
     *
     * @param transaction * $transaction
     * @return void
     */
    function deleteTransaction(TransactionInterface $transaction);

    /**
     * Finds one transaction by the given criteria
     *
     * @param array $criteria
     * @return TransactionInterface
     */
    function findTransactionBy(array $criteria);

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
    function updateTransaction(TransactionInterface $transaction);
}