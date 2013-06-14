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

interface InvoiceElementManagerInterface
{
    /**
     * Creates an empty invoiceElement instance
     *
     * @return InvoiceElement
     */
    public function create();

    /**
     * Deletes a invoiceElement
     *
     * @param  Invoiceelement $invoiceeElement
     * @return void
     */
    public function delete(InvoiceElementInterface $invoiceElement);

    /**
     * Finds one invoiceElement by the given criteria
     *
     * @param  array                   $criteria
     * @return InvoiceelementInterface
     */
    public function findOneBy(array $criteria);

    /**
     * Finds one invoiceElement by the given criteria
     *
     * @param  array                   $criteria
     * @return InvoiceelementInterface
     */
    public function findBy(array $criteria);

    /**
     * Returns the invoiceElement's fully qualified class name
     *
     * @return string
     */
    public function getClass();

    /**
     * Updates a invoiceElement
     *
     * @param  Invoiceelement $invoiceElement
     * @return void
     */
    public function save(InvoiceElementInterface $invoiceElement);
}
