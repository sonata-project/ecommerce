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
    function setInvoice(InvoiceInterface $invoiceId);

    /**
     * Get invoice
     *
     * @return Doctrine\Common\Collections\Collection $invoice
     */
    function getInvoice();

    /**
     * Get invoiceId
     *
     * @return integer $invoiceId
     */
    function getInvoiceId();

    /**
     * Set orderElement
     *
     * @param OrderElement $orderElement
     */
    function setOrderElement(OrderElementInterface $orderElement);

    /**
     * Get orderElement
     *
     * @return OrderElement $orderElement
     */
    function getOrderElement();

    /**
     * Set quantity
     *
     * @param integer $quantity
     */
    function setQuantity($quantity);

    /**
     * Get quantity
     *
     * @return integer $quantity
     */
    function getQuantity();

    /**
     * Set price
     *
     * @param decimal $price
     */
    function setPrice($price);

    /**
     * Get price
     *
     * @return decimal $price
     */
    function getPrice();

    /**
     * Set vat
     *
     * @param decimal $vat
     */
    function setVat($vat);

    /**
     * Get vat
     *
     * @return decimal $vat
     */
    function getVat();

    /**
     * Set total
     *
     * @param decimal $total
     */
    function setTotal($total);

    /**
     * Get total
     *
     * @return decimal $total
     */
    function getTotal();

    /**
     * Set designation
     *
     * @param string $designation
     */
    function setDesignation($designation);

    /**
     * Get designation
     *
     * @return string $designation
     */
    function getDesignation();

    /**
     * Set description
     *
     * @param text $description
     */
    function setDescription($description);

    /**
     * Get description
     *
     * @return text $description
     */
    function getDescription();

}