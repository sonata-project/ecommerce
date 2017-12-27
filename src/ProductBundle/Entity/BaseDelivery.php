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

namespace Sonata\ProductBundle\Entity;

use Sonata\Component\Delivery\BaseServiceDelivery;
use Sonata\Component\Product\DeliveryInterface;
use Sonata\Component\Product\ProductInterface;

abstract class BaseDelivery implements DeliveryInterface
{
    /**
     * @var string
     */
    protected $code;

    /**
     * @var bool
     */
    protected $perItem;

    /**
     * @var string
     */
    protected $countryCode;

    /**
     * @var string
     */
    protected $zone;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s-%s%s',
            $this->getCode(),
            $this->getCountryCode(),
            $this->getZone() ? sprintf('%s', $this->getZone()) : ''
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setProduct(ProductInterface $product): void
    {
        $this->product = $product;
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code): void
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setPerItem($perItem): void
    {
        $this->perItem = $perItem;
    }

    /**
     * {@inheritdoc}
     */
    public function getPerItem()
    {
        return $this->perItem;
    }

    /**
     * {@inheritdoc}
     */
    public function setCountryCode($countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setZone($zone): void
    {
        $this->zone = $zone;
    }

    /**
     * {@inheritdoc}
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($enabled): void
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
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt = null): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function prePersist(): void
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return array
     */
    public static function getStatusList()
    {
        return BaseServiceDelivery::getStatusList();
    }

    /**
     * @return array
     */
    public static function getValidationStatusList()
    {
        return array_keys(self::getStatusList());
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'code' => $this->code,
            'perItem' => $this->perItem,
            'countryCode' => $this->countryCode,
            'zone' => $this->zone,
            'enabled' => $this->enabled,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fromArray($array): void
    {
        if (array_key_exists('code', $array)) {
            $this->code = $array['code'];
        }

        if (array_key_exists('perItem', $array)) {
            $this->perItem = $array['perItem'];
        }

        if (array_key_exists('countryCode', $array)) {
            $this->countryCode = $array['countryCode'];
        }

        if (array_key_exists('zone', $array)) {
            $this->zone = $array['zone'];
        }

        if (array_key_exists('enabled', $array)) {
            $this->enabled = $array['enabled'];
        }
    }
}
