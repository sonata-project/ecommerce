<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Invoice;


interface InvoiceManagerInterface
{

    /**
     * Creates an empty invoice instance
     *
     * @return Invoice
     */
    function createInvoice();

    /**
     * Deletes a invoice
     *
     * @param Invoice $invoice
     * @return void
     */
    function deleteInvoice(InvoiceInterface $invoice);

    /**
     * Finds one invoice by the given criteria
     *
     * @param array $criteria
     * @return InvoiceInterface
     */
    function findInvoiceBy(array $criteria);

    /**
     * Returns the invoice's fully qualified class name
     *
     * @return string
     */
    function getClass();

    /**
     * Updates a invoice
     *
     * @param Invoice $invoice
     * @return void
     */
    function updateInvoice(InvoiceInterface $invoice);
}