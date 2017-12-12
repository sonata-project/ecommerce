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

namespace Sonata\Component\Product;

interface PackageInterface
{
    /**
     * Set productId.
     *
     * @param ProductInterface $product
     */
    public function setProduct(ProductInterface $product);

    /**
     * Get productId.
     *
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * Set width.
     *
     * @param float $width
     */
    public function setWidth($width);

    /**
     * Get width.
     *
     * @return float $width
     */
    public function getWidth();

    /**
     * Set height.
     *
     * @param float $height
     */
    public function setHeight($height);

    /**
     * Get height.
     *
     * @return float $height
     */
    public function getHeight();

    /**
     * Set length.
     *
     * @param float $length
     */
    public function setLength($length);

    /**
     * Get length.
     *
     * @return float $length
     */
    public function getLength();

    /**
     * Set weight.
     *
     * @param float $weight
     */
    public function setWeight($weight);

    /**
     * Get weight.
     *
     * @return float $weight
     */
    public function getWeight();

    /**
     * Set enabled.
     *
     * @param bool $enabled
     */
    public function setEnabled($enabled);

    /**
     * Get enabled.
     *
     * @return bool $enabled
     */
    public function getEnabled();

    /**
     * Set updatedAt.
     *
     * @param \Datetime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * Get updatedAt.
     *
     * @return \Datetime $updatedAt
     */
    public function getUpdatedAt();

    /**
     * Set createdAt.
     *
     * @param \Datetime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null);

    /**
     * Get createdAt.
     *
     * @return \Datetime $createdAt
     */
    public function getCreatedAt();
}
