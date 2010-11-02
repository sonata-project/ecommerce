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

use Sonata\Component\Product\ProductInterface;


abstract class BaseProduct implements ProductInterface {

    protected $parent;

    protected $enabled;

    protected $options;

    protected $name;

    protected $vat;

    protected $price;

    protected $variations = array();

    public function setParent($parent) {
        $this->parent = $parent;
    }

    public function addVariation($variation) {
        $this->variations[] = $variation;
    }

    public function getVariations() {
        return $this->variations;
    }

    public function setVariations($variations) {
        $this->variations = $variations;
    }

    public function setOptions($options) {
        $this->options = $options;
    }

    public function setVat($vat) {
        $this->vat = $vat;
    }

    public function setPrice($price) {
        $this->price = $price;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function isVariation() {
        return $this->parent !== null;
    }

    public function isRecurrentPayment() {
        return false;
    }

    public function setEnabled($enabled) {
        $this->enabled = $enabled;
    }

    public function getEnabled() {
        return $this->enabled;
    }

    public function getOptions() {

        return $this->options;
    }

    public function getParent() {
        return $this->parent;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function getVat() {
        return $this->vat;
    }

    public function getPrice() {

        return $this->price;
    }

    public function getId() {
       
        return $this->id;
    }

}
