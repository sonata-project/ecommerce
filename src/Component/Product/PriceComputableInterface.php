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

namespace Sonata\Component\Product;

/**
 * Interface PriceComputableInterface.
 *
 * This interface describes required fields for price computation
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
interface PriceComputableInterface
{
    /**
     * Returns the unit price.
     *
     * if $vat = true, returns the unit price with vat
     *
     * @param bool $vat
     *
     * @return float
     */
    public function getUnitPrice($vat = false);

    /**
     * @param float $price
     */
    public function setPrice($price);

    /**
     * Returns price of the element (including quantity).
     *
     * @param bool $vat
     *
     * @return float
     */
    public function getPrice($vat = false);

    /**
     * @param float $vatRate
     */
    public function setVatRate($vatRate);

    /**
     * @return float
     */
    public function getVatRate();

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity);

    /**
     * @return int
     */
    public function getQuantity();
}
