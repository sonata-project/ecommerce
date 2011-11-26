<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Product;

use Sonata\Component\Product\DeliveryInterface;

interface ProductInterface
{
    /**
     * @abstract
     * @return integer the product id
     */
    function getId();

    /**
     * @abstract
     * @return float the product price
     */
    function getPrice();

    /**
     * @abstract
     * @param  $price the product price
     * @return void
     */
    function setPrice($price);

    /**
     * @abstract
     * @return float the vat price
     */
    function getVat();

    /**
     * @abstract
     * @param $vat the product vat
     * @return void
     */
    function setVat($vat);

    /**
     * @abstract
     * @return string the product name
     */
    function getName();

    /**
     * @abstract
     * @return
     */
    function setName($name);

    /**
     * @abstract
     * @return string the product name
     */
    function getParent();

    /**
     * @abstract
     * @return
     */
    function setParent(ProductInterface $parent);

    /**
     * @abstract
     * @return array the product options
     */
    function getOptions();

    /**
     * @abstract
     * @param  $option array
     * @return void
     */
    function setOptions(array $options);

    /**
     * @abstract
     * @return boolean , true is the product is enabled (ready to be sell)
     */
    function getEnabled();

    /**
     * @abstract
     */
    function setEnabled($enabled);

    /**
     * Return true if the product is recurrent
     * @abstract
     * @return void
     */
    function isRecurrentPayment();

    /**
     * Return true if the product is a variation, linked to a main product
     *
     * @abstract
     * @return void
     */
    function isVariation();

    /**
     * @abstract
     * @return string
     */
    function getDescription();

    /**
     * @abstract
     * @param \Sonata\Component\Product\DeliveryInterface $delivery
     */
    function addDelivery(DeliveryInterface $delivery);

    /**
     * @abstract
     * @return array
     */
    function getDelivery();
}
