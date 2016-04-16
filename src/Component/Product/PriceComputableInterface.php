<?php

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
     * Sets unit price.
     *
     * @param float $unitPrice
     */
    public function setUnitPrice($unitPrice);

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
     * Sets price.
     *
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
     * Sets if price is including VAT.
     *
     * @param bool $priceIncludingVat
     */
    public function setPriceIncludingVat($priceIncludingVat);

    /**
     * Returns if price is including VAT.
     *
     * @return bool
     */
    public function isPriceIncludingVat();

    /**
     * Sets VAT rate.
     *
     * @param float $vatRate
     */
    public function setVatRate($vatRate);

    /**
     * Gets VAT rate.
     *
     * @return float
     */
    public function getVatRate();

    /**
     * Sets quantity.
     *
     * @param int $quantity
     */
    public function setQuantity($quantity);

    /**
     * Gets quantity.
     *
     * @return int $quantity
     */
    public function getQuantity();
}
