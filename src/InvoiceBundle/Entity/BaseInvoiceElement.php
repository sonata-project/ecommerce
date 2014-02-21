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
     * @var float $price
     */
    protected $price;

    /**
     * @var float $unitPriceExcl
     */
    protected $unitPriceExcl;

    /**
     * @var float $unitPriceInc
     */
    protected $unitPriceInc;

    /**
     * @var float $vatRate
     */
    protected $vatRate;

    /**
     * @var float $total
     */
    protected $total;

    /**
     * @var string $designation
     */
    protected $designation;

    /**
     * @var string $description
     */
    protected $description;

    /**
     * Set invoiceId
     *
     * @param InvoiceInterface $invoice
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
     * @param OrderElementInterface $orderElement
     */
    public function setOrderElement(OrderElementInterface $orderElement)
    {
        $this->orderElement = $orderElement;
    }

    /**
     * Get orderElement
     *
     * @return OrderElementInterface $orderElement
     */
    public function getOrderElement()
    {
        return $this->orderElement;
    }

    /**
     * {@inheritdoc}
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice($vat = false)
    {
        $unitPrice = $this->getUnitPriceExcl();

        if ($vat) {
            $unitPrice = $this->getUnitPriceInc();
        }

        return bcmul($unitPrice, $this->getQuantity());
    }

    /**
     * {@inheritdoc}
     */
    public function setVatRate($vatRate)
    {
        $this->vatRate = $vatRate;
    }

    /**
     * {@inheritdoc}
     */
    public function getVatRate()
    {
        return $this->vatRate;
    }

    /**
     * Set total
     *
     * @param float $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * {@inheritdoc}
     */
    public function setUnitPriceExcl($unitPriceExcl)
    {
        $this->unitPriceExcl = $unitPriceExcl;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnitPriceExcl()
    {
        return $this->unitPriceExcl;
    }

    /**
     * {@inheritdoc}
     */
    public function setUnitPriceInc($unitPriceInc)
    {
        $this->unitPriceInc = $unitPriceInc;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnitPriceInc()
    {
        return $this->unitPriceInc;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnitPrice($vat = false)
    {
        return $vat ? $this->getUnitPriceInc() : $this->getUnitPriceExcl();
    }

    /**
     * Get total
     *
     * @param boolean $vat
     *
     * @return float $total
     */
    public function getTotal($vat = true)
    {
        return bcmul($this->getUnitPrice($vat), $this->getQuantity());
    }

    /**
     * Returns VAT element amount
     *
     * @return float
     */
    public function getVatAmount()
    {
        return bcsub($this->getTotal(true), $this->getTotal(false));
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
     * string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }
}
