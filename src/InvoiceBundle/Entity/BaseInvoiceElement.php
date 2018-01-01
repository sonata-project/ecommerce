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
use Sonata\Component\Tests\Product\OrderElement;

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
     * {@inheritdoc}
     */
    public function setInvoice(InvoiceInterface $invoice): void
    {
        $this->invoice = $invoice;
    }

    /**
     * {@inheritdoc}
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderElement(OrderElementInterface $orderElement): void
    {
        $this->orderElement = $orderElement;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderElement()
    {
        return $this->orderElement;
    }

    /**
     * {@inheritdoc}
     */
    public function setQuantity($quantity): void
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
    public function setPrice($price): void
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
    public function setVatRate($vatRate): void
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
     * @param float $total
     */
    public function setTotal($total): void
    {
        $this->total = $total;
    }

    /**
     * {@inheritdoc}
     */
    public function setUnitPriceExcl($unitPriceExcl): void
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
    public function setUnitPriceInc($unitPriceInc): void
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function setDesignation($designation): void
    {
        $this->designation = $designation;
    }

    /**
     * {@inheritdoc}
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }
}
