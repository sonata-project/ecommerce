<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sonata\Component\Invoice;

use Sonata\Component\Order\OrderElementInterface;
use Sonata\Component\Invoice\InvoiceInterface;

interface InvoiceElementInterface
{
    /**
     * Set invoiceId
     *
     * @param InvoiceInterface $invoice
     */
    public function setInvoice(InvoiceInterface $invoice);

    /**
     * Get invoice
     *
     * @return InvoiceInterface $invoice
     */
    public function getInvoice();

    /**
     * Set orderElement
     *
     * @param OrderElementInterface $orderElement
     */
    public function setOrderElement(OrderElementInterface $orderElement);

    /**
     * Get orderElement
     *
     * @return OrderElementInterface $orderElement
     */
    public function getOrderElement();

    /**
     * Set quantity
     *
     * @param integer $quantity
     */
    public function setQuantity($quantity);

    /**
     * Get quantity
     *
     * @return integer $quantity
     */
    public function getQuantity();

    /**
     * Set price
     *
     * @param float $price
     */
    public function setPrice($price);

    /**
     * Get price
     *
     * @return float $price
     */
    public function getPrice();

    /**
     * Set vat
     *
     * @param float $vat
     */
    public function setVat($vat);

    /**
     * Get vat
     *
     * @return float $vat
     */
    public function getVat();

    /**
     * Set total
     *
     * @param float $total
     */
    public function setTotal($total);

    /**
     * Get total
     *
     * @return float $total
     */
    public function getTotal();

    /**
     * Set designation
     *
     * @param string $designation
     */
    public function setDesignation($designation);

    /**
     * Get designation
     *
     * @return string $designation
     */
    public function getDesignation();

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description);

    /**
     * Get description
     *
     * @return string $description
     */
    public function getDescription();

}
