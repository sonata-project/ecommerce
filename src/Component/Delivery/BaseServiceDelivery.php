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
        return [
            self::STATUS_OPEN => 'status_open',
            self::STATUS_PENDING => 'status_pending',
            self::STATUS_SENT => 'status_sent',
            self::STATUS_CANCELLED => 'status_cancelled',
            self::STATUS_COMPLETED => 'status_completed',
            self::STATUS_RETURNED => 'status_returned',
        ];
    }

    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code): void
    {
        $this->code = $code;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    public function setVatRate($vat): void
    {
        $this->vat = $vat;
    }

    public function getVatRate()
    {
        return $this->vat;
    }

    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

    /**
     * @param array $options
     */
    public function setOptions($options): void
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

    public function getTotal(BasketInterface $basket, $vat = false)
    {
        if ($vat) {
            return bcadd((string) ($this->getPrice() * (1 + $this->getVatRate() / 100)), '0', 2);
        }

        return bcadd((string) $this->getPrice(), '0', 2);
    }

    public function getVatAmount(BasketInterface $basket)
    {
        $vat = (string) ($this->getTotal($basket, true) - $this->getTotal($basket));

        return bcadd($vat, '0', 2);
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority): void
    {
        $this->priority = $priority;
    }

    public function getPriority()
    {
        return $this->priority;
    }
}
