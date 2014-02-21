<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Basket;

use Sonata\Component\Currency\CurrencyInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\ProductManagerInterface;
use Sonata\Component\Product\ProductDefinition;
use Sonata\Component\Product\PriceComputableInterface;

interface BasketElementInterface extends PriceComputableInterface
{
    /**
     * the position in the basket stack
     *
     * @param integer $position
     */
    public function setPosition($position);

    /**
     * return the pos of the current basket element
     *
     * @return int
     */
    public function getPosition();

    /**
     * return the name of the basket element
     *
     * @return string
     */
    public function getName();

    /**
     * Define the related product
     *
     * @param string           $productCode
     * @param ProductInterface $product
     */
    public function setProduct($productCode, ProductInterface $product);

    /**
     * Return the related product
     *
     * @return \Sonata\Component\Product\ProductInterface
     */
    public function getProduct();

    /**
     * return the product id
     *
     * @return integer
     */
    public function getProductId();

    /**
     * Never call this method, use the setProduct instead. This method is only used
     * by the form framework
     *
     * @param int $productId
     */
    public function setProductId($productId);

    /**
     * Returns the VAT amount
     *
     * @return $float
     */
    public function getVatAmount();

    /**
     * Sets product unit price
     *
     * @param float $unitPrice
     */
    public function setUnitPrice($unitPrice);

    /**
     * Sets if current price is including VAT
     *
     * @param float $priceIncludingVat
     */
    public function setPriceIncludingVat($priceIncludingVat);

    /**
     * Returns if price is including VAT
     *
     * @return boolean
     */
    public function isPriceIncludingVat();

    /**
     * Return the total (price * quantity)
     *
     * if $vat = true, return the price with vat
     *
     * @param boolean $vat Returns price including VAT?
     *
     * @return float
     */
    public function getTotal($vat = false);

    /**
     * return the basket element options array
     *
     * @return array
     */
    public function getOptions();

    /**
     * return a option value depends on the $name
     *
     * @param  string $name
     * @param  mixed  $default Default value if option not found
     * @return mixed
     */
    public function getOption($name, $default = null);

    /**
     * Define the option value
     *
     * @param string $name
     * @param mixed  $value
     */
    public function setOption($name, $value);

    /**
     * Check if the basket element is still valid
     *
     * @return boolean
     */
    public function isValid();

    /**
     * @param boolean $delete
     */
    public function setDelete($delete);

    /**
     * @return boolean
     */
    public function getDelete();

    /**
     * @param \Sonata\Component\Product\ProductDefinition $productDefinition
     */
    public function setProductDefinition(ProductDefinition $productDefinition);

    /**
     * @return \Sonata\Component\Product\ProductManagerInterface
     */
    public function getProductManager();

    /**
     * @return \Sonata\Component\Product\ProductProviderInterface
     */
    public function getProductProvider();

    /**
     * @return string
     */
    public function getProductCode();
}
