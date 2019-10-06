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

namespace Sonata\Component\Basket;

use Sonata\Component\Product\PriceComputableInterface;
use Sonata\Component\Product\ProductDefinition;
use Sonata\Component\Product\ProductInterface;

interface BasketElementInterface extends PriceComputableInterface
{
    /**
     * the position in the basket stack.
     *
     * @param int $position
     */
    public function setPosition($position);

    /**
     * return the pos of the current basket element.
     *
     * @return int
     */
    public function getPosition();

    /**
     * return the name of the basket element.
     *
     * @return string
     */
    public function getName();

    /**
     * Define the related product.
     *
     * @param string $productCode
     */
    public function setProduct($productCode, ProductInterface $product);

    /**
     * Return the related product.
     *
     * @return \Sonata\Component\Product\ProductInterface
     */
    public function getProduct();

    /**
     * return the product id.
     *
     * @return int
     */
    public function getProductId();

    /**
     * Never call this method, use the setProduct instead. This method is only used
     * by the form framework.
     */
    public function setProductId(int $productId): void;

    /**
     * Returns the VAT amount.
     *
     * @return float
     */
    public function getVatAmount();

    /**
     * Sets product unit price.
     *
     * @param float $unitPrice
     */
    public function setUnitPrice($unitPrice);

    /**
     * Sets if current price is including VAT.
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
     * Return the total (price * quantity).
     *
     * if $vat = true, return the price with vat
     *
     * @param bool $vat Returns price including VAT?
     *
     * @return float
     */
    public function getTotal($vat = false);

    /**
     * return the basket element options array.
     *
     * @return array
     */
    public function getOptions();

    /**
     * return a option value depends on the $name.
     *
     * @param string $name
     * @param mixed  $default Default value if option not found
     *
     * @return mixed
     */
    public function getOption($name, $default = null);

    /**
     * Define the option value.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function setOption($name, $value);

    /**
     * Check if the basket element is still valid.
     *
     * @return bool
     */
    public function isValid();

    /**
     * @param bool $delete
     */
    public function setDelete($delete);

    /**
     * @return bool
     */
    public function getDelete();

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
