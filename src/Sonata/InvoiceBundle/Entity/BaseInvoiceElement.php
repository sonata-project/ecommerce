<?php

namespace Sonata\InvoiceBundle\Entity;

/**
 * Sonata\InvoiceBundle\Entity\BaseInvoiceElement
 */
abstract class BaseInvoiceElement
{
    /**
     * @var integer $invoiceId
     */
    protected $invoiceId;

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
     * @var Sonata\InvoiceBundle\Entity\Invoice
     */
    protected $invoice;

    /**
     * Set invoiceId
     *
     * @param integer $invoiceId
     */
    public function setInvoiceId($invoiceId)
    {
        $this->invoiceId = $invoiceId;
    }

    /**
     * Get invoiceId
     *
     * @return integer $invoiceId
     */
    public function getInvoiceId()
    {
        return $this->invoiceId;
    }

    /**
     * Set orderElement
     *
     * @param OrderElement $orderElement
     */
    public function setOrderElement($orderElement)
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

    /**
     * Add invoice
     *
     * @param Sonata\InvoiceBundle\Entity\Invoice $invoice
     */
    public function addInvoice(\Application\Sonata\InvoiceBundle\Entity\Invoice $invoice)
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
     * @param Sonata\OrderBundle\Entity\OrderElement $orderElement
     */
    public function addOrderElement(\Application\Sonata\OrderBundle\Entity\OrderElement $orderElement)
    {
        $this->order_element[] = $orderElement;
    }

}