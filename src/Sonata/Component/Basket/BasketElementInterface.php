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

use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\ProductManagerInterface;

interface BasketElementInterface
{

    /**
     * the position in the basket stack
     *
     * @param unknown_type $pos
     */
    function setPos($pos);

    /**
     * return the pos of the current basket element
     *
     * @return int
     */
    function getPos();

    /**
     * return the name of the basket element
     *
     * @return string
     */
    function getName();

    /**
     * Define the related product
     *
     * @param Product $product
     * @return BasketElement
     */
    function setProduct(ProductInterface $product);

    /**
     * Return the related product
     *
     * @return Product
     */
    function getProduct();

    /**
     * return the product id
     * 
     * @return null
     */
    function getProductId();

    /**
     * Never call this method, use the setProduct instead. This method is only used
     * by the form framework
     * 
     * @param  $productId
     * @return void
     */
    function setProductId($productId);

    /**
     * Return the VAT amount
     *
     *
     * @return $float
     */
    function getVatAmount();

    /**
     * Return the VAT
     *
     *
     * @return $float
     */
    function getVat();

    /**
     * Return the price
     *
     * if $tva = true, return the price with vat
     *
     * @param boolean $tva
     * @return float
     */
    function getUnitPrice($tva = false);

    /**
     * Return the total (price * quantity)
     *
     * if $tva = true, return the price with vat
     *
     * @param boolean $tva
     * @return float
     */
    function getTotal($tva = false);

    /**
     * return the basket element options array
     *
     * @return array
     */
    function getOptions();

    /**
     * return a option value depends on the $name
     *
     * @param string $name
     * @return mixed
     */
    function getOption($name);

    /**
     * Define the option value
     *
     * @param string $name
     * @param mixed $value
     */
    function setOption($name, $value);

    /**
     * Define the price
     *
     * @param float $price
     */
    function setPrice($price);

    /**
     * Define the quantity
     *
     * @param float $price
     */
    function setQuantity($quantity);

    /**
     * return the quantity
     * 
     * @return int
     */
    function getQuantity();

    /**
     * Check if the basket element is still valid
     *
     * @return boolean
     */
    function isValid();

    /**
     * @abstract
     * @param boolean $delete
     * @return void
     */
    function setDelete($delete);

    /**
     * @abstract
     * @return booelan
     */
    function getDelete();

    /**
     * @abstract
     * @param \Sonata\Component\Product\ProductManagerInterface $productManager
     * @return void
     */
    function setProductManager(ProductManagerInterface $productManager);

    /**
     * @abstract
     * @return \Sonata\Component\Product\ProductManagerInterface
     */
    function getProductManager();

    /**
     * @abstract
     * @param string $productCode
     * @return void
     */
    function setProductCode($productCode);

    /**
     * @abstract
     * @return string
     */
    function getProductCode();
}
