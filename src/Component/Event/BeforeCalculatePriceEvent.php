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

namespace Sonata\Component\Event;

use Sonata\Component\Currency\CurrencyInterface;
use Sonata\Component\Product\ProductInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class BeforeCalculatePriceEvent extends Event
{
    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var CurrencyInterface
     */
    protected $currency;

    /**
     * @var bool
     */
    protected $vat;

    /**
     * @var int
     */
    protected $quantity;

    /**
     * @param ProductInterface  $product
     * @param CurrencyInterface $currency
     * @param bool              $vat
     * @param int               $quantity
     */
    public function __construct(ProductInterface $product, CurrencyInterface $currency, $vat, $quantity)
    {
        $this->product = $product;
        $this->currency = $currency;
        $this->vat = $vat;
        $this->quantity = $quantity;
    }

    /**
     * @return \Sonata\Component\Currency\CurrencyInterface
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return \Sonata\Component\Product\ProductInterface
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param bool $vat
     */
    public function setVat($vat): void
    {
        $this->vat = $vat;
    }

    /**
     * @return bool
     */
    public function getVat()
    {
        return $this->vat;
    }
}
