<?php

namespace Sonata\Bundle\InvoiceBundle\Entity;

/**
 * Sonata\Bundle\InvoiceBundle\Entity\BaseInvoiceElement
 */
abstract class BaseInvoiceElement
{
    /**
     * @var integer $invoice_id
     */
    private $invoice_id;

    /**
     * @var integer $order_element_id
     */
    private $order_element_id;

    /**
     * @var integer $quantity
     */
    private $quantity;

    /**
     * @var decimal $price
     */
    private $price;

    /**
     * @var decimal $vat
     */
    private $vat;

    /**
     * @var decimal $total
     */
    private $total;

    /**
     * @var string $designation
     */
    private $designation;

    /**
     * @var text $description
     */
    private $description;

    /**
     * @var Sonata\Bundle\InvoiceBundle\Entity\Invoice
     */
    private $invoice;

    /**
     * @var Sonata\Bundle\OrderBundle\Entity\OrderElement
     */
    private $order_element;

    /**
     * Set invoice_id
     *
     * @param integer $invoiceId
     */
    public function setInvoiceId($invoiceId)
    {
        $this->invoice_id = $invoiceId;
    }

    /**
     * Get invoice_id
     *
     * @return integer $invoiceId
     */
    public function getInvoiceId()
    {
        return $this->invoice_id;
    }

    /**
     * Set order_element_id
     *
     * @param integer $orderElementId
     */
    public function setOrderElementId($orderElementId)
    {
        $this->order_element_id = $orderElementId;
    }

    /**
     * Get order_element_id
     *
     * @return integer $orderElementId
     */
    public function getOrderElementId()
    {
        return $this->order_element_id;
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

    /**
     * Add invoice
     *
     * @param Sonata\Bundle\InvoiceBundle\Entity\Invoice $invoice
     */
    public function addInvoice(\Application\InvoiceBundle\Entity\Invoice $invoice)
    {
        $this->invoice[] = $invoice;
    }

    /**
     * Get invoice
     *
     * @return Doctrine\Common\Collections\Collection $invoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * Add order_element
     *
     * @param Sonata\Bundle\OrderBundle\Entity\OrderElement $orderElement
     */
    public function addOrderElement(\Application\OrderBundle\Entity\OrderElement $orderElement)
    {
        $this->order_element[] = $orderElement;
    }

    /**
     * Get order_element
     *
     * @return Doctrine\Common\Collections\Collection $orderElement
     */
    public function getOrderElement()
    {
        return $this->order_element;
    }
}