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

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class AfterCalculatePriceEvent extends BeforeCalculatePriceEvent
{
    /**
     * @var float
     */
    protected $price;

    /**
     * @param ProductInterface  $product
     * @param CurrencyInterface $currency
     * @param bool              $vat
     * @param int               $quantity
     * @param float             $price
     */
    public function __construct(ProductInterface $product, CurrencyInterface $currency, $vat, $quantity, $price)
    {
        parent::__construct($product, $currency, $vat, $quantity);
        $this->price = $price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }
}
