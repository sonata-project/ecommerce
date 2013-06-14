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
     * @param integer $invoiceId
     */
    public function setInvoice(InvoiceInterface $invoiceId);

    /**
     * Get invoice
     *
     * @return \Sonata\Component\Invoice\InvoiceInterface $invoice
     */
    public function getInvoice();

    /**
     * Set orderElement
     *
     * @param OrderElement $orderElement
     */
    public function setOrderElement(OrderElementInterface $orderElement);

    /**
     * Get orderElement
     *
     * @return OrderElement $orderElement
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
     * @param decimal $price
     */
    public function setPrice($price);

    /**
     * Get price
     *
     * @return decimal $price
     */
    public function getPrice();

    /**
     * Set vat
     *
     * @param decimal $vat
     */
    public function setVat($vat);

    /**
     * Get vat
     *
     * @return decimal $vat
     */
    public function getVat();

    /**
     * Set total
     *
     * @param decimal $total
     */
    public function setTotal($total);

    /**
     * Get total
     *
     * @return decimal $total
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
     * @param text $description
     */
    public function setDescription($description);

    /**
     * Get description
     *
     * @return text $description
     */
    public function getDescription();

}
