<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\InvoiceBundle\Entity;

use Sonata\Component\Invoice\InvoiceInterface;
use Sonata\Component\Invoice\InvoiceElementInterface;
use Sonata\Component\Order\OrderElementInterface;

/**
 * Sonata\InvoiceBundle\Entity\BaseInvoiceElement
 */
abstract class BaseInvoiceElement implements InvoiceElementInterface
{
    /**
     * @var InvoiceInterface $invoiceId
     */
    protected $invoice;

    /**
     * @var OrderElement $orderElement
     */
    protected $orderElement;

    /**
     * @var integer $quantity
     */
    protected $quantity;

    /**
     * @var decimal $price
     */
    protected $price;

    /**
     * @var decimal $vat
     */
    protected $vat;

    /**
     * @var decimal $total
     */
    protected $total;

    /**
     * @var string $designation
     */
    protected $designation;

    /**
     * @var text $description
     */
    protected $description;

    /**
     * Set invoiceId
     *
     * @param integer $invoice
     */
    public function setInvoice(InvoiceInterface $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get invoiceId
     *
     * @return integer $invoiceId
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * Set orderElement
     *
     * @param OrderElement $orderElement
     */
    public function setOrderElement(OrderElementInterface $orderElement)
    {
        $this->orderElement = $orderElement;
    }

    /**
     * Get orderElement
     *
     * @return OrderElement $orderElement
     */
    public function getOrderElement()
    {
        return $this->orderElement;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * Get quantity
     *
     * @return integer $quantity
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set price
     *
     * @param decimal $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * Get price
     *
     * @return decimal $price
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set vat
     *
     * @param decimal $vat
     */
    public function setVat($vat)
    {
        $this->vat = $vat;
    }

    /**
     * Get vat
     *
     * @return decimal $vat
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * Set total
     *
     * @param decimal $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * Get total
     *
     * @return decimal $total
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set designation
     *
     * @param string $designation
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;
    }

    /**
     * Get designation
     *
     * @return string $designation
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return text $description
     */
    public function getDescription()
    {
        return $this->description;
    }

}