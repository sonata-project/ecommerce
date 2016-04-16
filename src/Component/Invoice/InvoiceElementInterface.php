<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Invoice;

use Sonata\Component\Order\OrderElementInterface;
use Sonata\Component\Product\PriceComputableInterface;

interface InvoiceElementInterface extends PriceComputableInterface
{
    /**
     * Set invoiceId.
     *
     * @param InvoiceInterface $invoice
     */
    public function setInvoice(InvoiceInterface $invoice);

    /**
     * Get invoice.
     *
     * @return InvoiceInterface $invoice
     */
    public function getInvoice();

    /**
     * Set orderElement.
     *
     * @param OrderElementInterface $orderElement
     */
    public function setOrderElement(OrderElementInterface $orderElement);

    /**
     * Get orderElement.
     *
     * @return OrderElementInterface $orderElement
     */
    public function getOrderElement();

    /**
     * Set total.
     *
     * @param float $total
     */
    public function setTotal($total);

    /**
     * Get total.
     *
     * @return float $total
     */
    public function getTotal();

    /**
     * Set designation.
     *
     * @param string $designation
     */
    public function setDesignation($designation);

    /**
     * Get designation.
     *
     * @return string $designation
     */
    public function getDesignation();

    /**
     * Set description.
     *
     * @param string $description
     */
    public function setDescription($description);

    /**
     * Get description.
     *
     * @return string $description
     */
    public function getDescription();
}
