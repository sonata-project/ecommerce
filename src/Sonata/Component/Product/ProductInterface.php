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

interface ProductInterface {

    /**
     * @abstract
     * @return integer the product id
     */
    public function getId();

    /**
     * @abstract
     * @param  $id the product id
     * @return void
     */
    public function setId($id);

    /**
     * @abstract
     * @return float the product price
     */
    public function getPrice();

    /**
     * @abstract
     * @param  $price the product price
     * @return void
     */
    public function setPrice($price);

    /**
     * @abstract
     * @return float the vat price
     */
    public function getVat();

    /**
     * @abstract
     * @param $vat the product vat
     * @return void
     */
    public function setVat($vat);

    /**
     * @abstract
     * @return string the product name
     */
    public function getName();

    /**
     * @abstract
     * @return
     */
    public function setName($name);

    /**
     * @abstract
     * @return string the product name
     */
    public function getParent();

    /**
     * @abstract
     * @return
     */
    public function setParent($parent);

    /**
     * @abstract
     * @return array the product options
     */
    public function getOptions();

    /**
     * @abstract
     * @param  $option array
     * @return void
     */
    public function setOptions($options);

    /**
     * @abstract
     * @return boolean , true is the product is enabled (ready to be sell)
     */
    public function getEnabled();

    /**
     * @abstract
     */
    public function setEnabled($enabled);

    /**
     * Return true if the product is recurrent
     * @abstract
     * @return void
     */
    public function isRecurrentPayment();

    /**
     * Return true if the product is a variation, linked to a main product
     *
     * @abstract
     * @return void
     */
    public function isVariation();

}
