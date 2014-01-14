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
     * @var float $unitPrice
     */
    protected $unitPrice;

    /**
     * @var boolean
     */
    protected $priceIncludingVat;

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
        $price = $this->price;

        if (!$vat && true === $this->isPriceIncludingVat()) {
            $price = bcmul($price, bcsub(1, bcdiv($this->getVatRate(), 100)));
        }

        if ($vat && false === $this->isPriceIncludingVat()) {
            $price = bcmul($price, bcadd(1, bcdiv($this->getVatRate(), 100)));
        }

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsPriceIncludingVat($priceIncludingVat)
    {
        $this->priceIncludingVat = $priceIncludingVat;
    }

    /**
     * {@inheritdoc}
     */
    public function isPriceIncludingVat()
    {
        return $this->priceIncludingVat;
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
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnitPrice($vat = false)
    {
        $price = $this->unitPrice;

        if (!$vat && true === $this->isPriceIncludingVat()) {
            $price = bcmul($price, bcsub(1, bcdiv($this->getVatRate(), 100)));
        }

        if ($vat && false === $this->isPriceIncludingVat()) {
            $price = bcmul($price, bcadd(1, bcdiv($this->getVatRate(), 100)));
        }

        return $price;
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
        $total = $this->total;

        if (!$vat) {
            $total = bcmul($total, bcsub(1, bcdiv($this->getVatRate(), 100)));
        }

        return $total;
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
