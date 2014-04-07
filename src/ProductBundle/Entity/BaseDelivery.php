<?php
/*
 * This file is part of the Sonata package.
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

/**
 * Sonata\ProductBundle\Entity\BaseDelivery
 */
abstract class BaseDelivery implements DeliveryInterface
{
    /**
     * @var string $code
     */
    protected $code;

    /**
     * @var boolean $perItem
     */
    protected $perItem;

    /**
     * @var string $country
     */
    protected $countryCode;

    /**
     * @var string $zone
     */
    protected $zone;

    /**
     * @var boolean $enabled
     */
    protected $enabled;

    /**
     * @var \DateTime $updatedAt
     */
    protected $updatedAt;

    /**
     * @var \DateTime $createdAt
     */
    protected $createdAt;

    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * Set productId
     *
     * @param ProductInterface $product
     */
    public function setProduct(ProductInterface $product)
    {
        $this->product = $product;
    }

    /**
     * Get productId
     *
     * @return integer $productId
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set code
     *
     * @param $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Get class_name
     *
     * @return string $className
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set per_item
     *
     * @param boolean $perItem
     */
    public function setPerItem($perItem)
    {
        $this->perItem = $perItem;
    }

    /**
     * Get per_item
     *
     * @return boolean $perItem
     */
    public function getPerItem()
    {
        return $this->perItem;
    }

    /**
     * Set country code
     *
     * @param $countryCode
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
    }

    /**
     * Get country
     *
     * @return string $country
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Set zone
     *
     * @param string $zone
     */
    public function setZone($zone)
    {
        $this->zone = $zone;
    }

    /**
     * Get zone
     *
     * @return string $zone
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * Get enabled
     *
     * @return boolean $enabled
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function prePersist()
    {
        $this->createdAt = new \DateTime;
        $this->updatedAt = new \DateTime;
    }

    public function preUpdate()
    {
        $this->updatedAt = new \DateTime;
    }

    /**
     * return delivery status list
     *
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
        return array(
            'code'        => $this->code,
            'perItem'     => $this->perItem,
            'countryCode' => $this->countryCode,
            'zone'        => $this->zone,
            'enabled'     => $this->enabled,
        );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("%s-%s%s",
            $this->getCode(),
            $this->getCountryCode(),
            $this->getZone() ? sprintf('%s', $this->getZone()): ''
        );
    }

    /**
     * {@inheritdoc}
     */
    public function fromArray($array)
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
