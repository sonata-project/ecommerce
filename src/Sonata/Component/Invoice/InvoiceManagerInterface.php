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
    public function createInvoice();

    /**
     * Deletes a invoice
     *
     * @param  InvoiceInterface $invoice
     * @return void
     */
    public function deleteInvoice(InvoiceInterface $invoice);

    /**
     * Finds one invoice by the given criteria
     *
     * @param  array            $criteria
     * @return InvoiceInterface
     */
    public function findInvoiceBy(array $criteria);

    /**
     * Returns the invoice's fully qualified class name
     *
     * @return string
     */
    public function getClass();

    /**
     * Updates a invoice
     *
     * @param  InvoiceInterface $invoice
     * @return void
     */
    public function updateInvoice(InvoiceInterface $invoice);
}
