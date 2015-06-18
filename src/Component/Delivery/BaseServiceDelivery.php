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

use Sonata\Component\Basket\BasketInterface;

/**
 * A free delivery method, used this only for testing.
 */
abstract class BaseServiceDelivery implements ServiceDeliveryInterface
{
    /**
     * @var float
     */
    protected $vat;

    /**
     * @var float
     */
    protected $price;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var int
     */
    protected $priority;

    /**
     * return status list.
     *
     * @return array
     */
    public static function getStatusList()
    {
        return array(
            self::STATUS_OPEN       => 'status_open',
            self::STATUS_PENDING    => 'status_pending',
            self::STATUS_SENT       => 'status_sent',
            self::STATUS_CANCELLED  => 'status_cancelled',
            self::STATUS_COMPLETED  => 'status_completed',
            self::STATUS_RETURNED   => 'status_returned',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function setVatRate($vat)
    {
        $this->vat = $vat;
    }

    /**
     * {@inheritdoc}
     */
    public function getVatRate()
    {
        return $this->vat;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Returns the option $name.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getOption($name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal(BasketInterface $basket, $vat = false)
    {
        if ($vat) {
            return bcadd($this->getPrice() * (1 + $this->getVatRate() / 100), 0, 2);
        }

        return bcadd($this->getPrice(), 0, 2);
    }

    /**
     * {@inheritdoc}
     */
    public function getVatAmount(BasketInterface $basket)
    {
        $vat = $this->getTotal($basket, true) - $this->getTotal($basket);

        return bcadd($vat, 0, 2);
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->priority;
    }
}
