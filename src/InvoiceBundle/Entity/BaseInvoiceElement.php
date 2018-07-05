<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\InvoiceBundle\Entity;

use Sonata\Component\Invoice\InvoiceElementInterface;
use Sonata\Component\Invoice\InvoiceInterface;
use Sonata\Component\Order\OrderElementInterface;

abstract class BaseInvoiceElement implements InvoiceElementInterface
{
    /**
     * @var InvoiceInterface
     */
    protected $invoice;

    /**
     * @var OrderElement
     */
    protected $orderElement;

    /**
     * @var int
     */
    protected $quantity;

    /**
     * @var float
     */
    protected $price;

    /**
     * @var float
     */
    protected $unitPriceExcl;

    /**
     * @var float
     */
    protected $unitPriceInc;

    /**
     * @var float
     */
    protected $vatRate;

    /**
     * @var float
     */
    protected $total;

    /**
     * @var string
     */
    protected $designation;

    /**
     * @var string
     */
    protected $description;

    /**
     * Set invoiceId.
     *
     * @param InvoiceInterface $invoice
     */
    public function setInvoice(InvoiceInterface $invoice): void
    {
        $this->invoice = $invoice;
    }

    /**
     * Get invoiceId.
     *
     * @return InvoiceInterface $invoiceId
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * Set orderElement.
     *
     * @param OrderElementInterface $orderElement
     */
    public function setOrderElement(OrderElementInterface $orderElement): void
    {
        $this->orderElement = $orderElement;
    }

    /**
     * Get orderElement.
     *
     * @return OrderElement $orderElement
     */
    public function getOrderElement()
    {
        return $this->orderElement;
    }

    public function setQuantity($quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function setPrice($price): void
    {
        $this->price = $price;
    }

    public function getPrice($vat = false)
    {
        $unitPrice = $this->getUnitPriceExcl();

        if ($vat) {
            $unitPrice = $this->getUnitPriceInc();
        }

        return bcmul($unitPrice, $this->getQuantity());
    }

    public function setVatRate($vatRate): void
    {
        $this->vatRate = $vatRate;
    }

    public function getVatRate()
    {
        return $this->vatRate;
    }

    /**
     * Set total.
     *
     * @param float $total
     */
    public function setTotal($total): void
    {
        $this->total = $total;
    }

    public function setUnitPriceExcl($unitPriceExcl): void
    {
        $this->unitPriceExcl = $unitPriceExcl;
    }

    public function getUnitPriceExcl()
    {
        return $this->unitPriceExcl;
    }

    public function setUnitPriceInc($unitPriceInc): void
    {
        $this->unitPriceInc = $unitPriceInc;
    }

    public function getUnitPriceInc()
    {
        return $this->unitPriceInc;
    }

    public function getUnitPrice($vat = false)
    {
        return $vat ? $this->getUnitPriceInc() : $this->getUnitPriceExcl();
    }

    /**
     * Get total.
     *
     * @param bool $vat
     *
     * @return float $total
     */
    public function getTotal($vat = true)
    {
        return bcmul($this->getUnitPrice($vat), $this->getQuantity());
    }

    /**
     * Returns VAT element amount.
     *
     * @return float
     */
    public function getVatAmount()
    {
        return bcsub($this->getTotal(true), $this->getTotal(false));
    }

    /**
     * Set designation.
     *
     * @param string $designation
     */
    public function setDesignation($designation): void
    {
        $this->designation = $designation;
    }

    /**
     * Get designation.
     *
     * @return string $designation
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * Set description.
     *
     * @param string $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * Get description.
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }
}
