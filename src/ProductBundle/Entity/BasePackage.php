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

use Sonata\Component\Product\PackageInterface;
use Sonata\Component\Product\ProductInterface;

abstract class BasePackage implements PackageInterface
{
    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var float
     */
    protected $width;

    /**
     * @var float
     */
    protected $height;

    /**
     * @var float
     */
    protected $length;

    /**
     * @var float
     */
    protected $weight;

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

    public function setProduct(ProductInterface $product): void
    {
        $this->product = $product;
    }

    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set width.
     *
     * @param float $width
     */
    public function setWidth($width): void
    {
        $this->width = $width;
    }

    /**
     * Get width.
     *
     * @return float $width
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height.
     *
     * @param float $height
     */
    public function setHeight($height): void
    {
        $this->height = $height;
    }

    /**
     * Get height.
     *
     * @return float $height
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set length.
     *
     * @param float $length
     */
    public function setLength($length): void
    {
        $this->length = $length;
    }

    /**
     * Get length.
     *
     * @return float $length
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Set weight.
     *
     * @param float $weight
     */
    public function setWeight($weight): void
    {
        $this->weight = $weight;
    }

    /**
     * Get weight.
     *
     * @return float $weight
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set enabled.
     *
     * @param bool $enabled
     */
    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * Get enabled.
     *
     * @return bool $enabled
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
