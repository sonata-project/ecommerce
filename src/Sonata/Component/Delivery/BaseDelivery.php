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
abstract class BaseDelivery implements DeliveryInterface
{


    protected $vat;

    protected $price;

    protected $name;

    protected $code;

    protected $options;

    protected $enabled;

    protected $priority;

    /**
     * return status list
     *
     * @return array
     */
    public static function getStatusList()
    {

        return array(
            self::STATUS_OPEN       => 'open',
            self::STATUS_SENT       => 'sent',
            self::STATUS_CANCELLED  => 'cancelled',
            self::STATUS_COMPLETED  => 'delivered',
            self::STATUS_RETURNED   => 'returned',
        );
    }

    public function getCode()
    {

        return $this->code;
    }

    public function setCode($code)
    {

        $this->code = $code;
    }

    public function getName()
    {

        return $this->name;
    }

    public function setName($name)
    {

        $this->name = $name;
    }

    public function setVat($vat)
    {

        $this->vat = $vat;
    }

    public function getVat()
    {

        return $this->vat;
    }

    public function getPrice()
    {

        return $this->prive;
    }

    public function setPrice($price)
    {

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

    public function getOption($name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    public function getDeliveryPrice($basket, $vat = false)
    {

        if ($vat) {
            return bcadd($this->getPrice() * (1 + $this->getVat() / 100), 0, 2);
        }

        return bcadd($this->getPrice(), 0, 2);
    }

    public function getVatAmount($basket)
    {
        $vat = $this->getDeliveryPrice($basket, true) - $this->getDeliveryPrice($basket);

        return bcadd($vat, 0, 2);
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    public function getPriority()
    {
        return $this->priority;
    }
}