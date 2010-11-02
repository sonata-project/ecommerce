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
abstract class BaseDelivery implements DeliveryInterface {

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

    public function setOptions($options) {
        $this->options = $options;
    }

    public function getOptions() {
        return $this->options;
    }

    public function getDeliveryPrice($basket, $vat = false) {

        if ($vat) {
            return bcadd($this->getPrice() * (1 + $this->getVat() / 100), 0, 2);
        }

        return bcadd($this->getPrice(), 0, 2);
    }

    public function getVatAmount($basket) {
        $vat = $this->getDeliveryPrice($basket, true) - $this->getDeliveryPrice($basket);

        return bcadd($vat, 0, 2);
    }

    /**
     * return status list
     *
     * @return array
     */
    public static function getStatusList() {

        return array(
            self::STATUS_OPEN       => 'status_open',
            self::STATUS_SENT       => 'status_sent',
            self::STATUS_CANCELLED  => 'status_cancelled',
            self::STATUS_COMPLETED  => 'status_delivered',
            self::STATUS_RETURNED   => 'status_returned',
        );
        
    }
}