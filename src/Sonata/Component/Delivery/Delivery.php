<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Delivery;

/**
 * A free delivery method, used this only for testing
 *
 */
abstract class Delivery implements DeliveryInterface
{
    protected
        $vat,
        $price,
        $name,
        $code,
        $options;

    public function getCode() {

        return $this->code;
    }

    public function setCode($code) {

        $this->code = $code;
    }

    public function getName() {

        return $this->name;
    }

    public function setName($name) {

        $this->name = $name;
    }

    public function setVat($vat) {

        $this->vat = $vat;
    }

    public function getVat() {

        return $this->vat;
    }

    public function getPrice() {

        return $this->prive;
    }

    public function setPrice($price) {

        $this->prive = $price;
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function getOptions()
    {
        return $this->options;
    }

}