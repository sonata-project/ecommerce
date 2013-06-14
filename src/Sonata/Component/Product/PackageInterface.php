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

interface PackageInterface
{
    /**
     * Set productId
     *
     * @param integer $productId
     */
    public function setProductId($productId);

    /**
     * Get productId
     *
     * @return integer $productId
     */
    public function getProductId();

    /**
     * Set width
     *
     * @param decimal $width
     */
    public function setWidth($width);

    /**
     * Get width
     *
     * @return decimal $width
     */
    public function getWidth();

    /**
     * Set height
     *
     * @param decimal $height
     */
    public function setHeight($height);

    /**
     * Get height
     *
     * @return decimal $height
     */
    public function getHeight();

    /**
     * Set length
     *
     * @param decimal $length
     */
    public function setLength($length);

    /**
     * Get length
     *
     * @return decimal $length
     */
    public function getLength();

    /**
     * Set weight
     *
     * @param decimal $weight
     */
    public function setWeight($weight);

    /**
     * Get weight
     *
     * @return decimal $weight
     */
    public function getWeight();

    /**
     * Set enabled
     *
     * @param boolean $enabled
     */
    public function setEnabled($enabled);

    /**
     * Get enabled
     *
     * @return boolean $enabled
     */
    public function getEnabled();

    /**
     * Set updatedAt
     *
     * @param datetime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * Get updatedAt
     *
     * @return datetime $updatedAt
     */
    public function getUpdatedAt();

    /**
     * Set createdAt
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null);

    /**
     * Get createdAt
     *
     * @return datetime $createdAt
     */
    public function getCreatedAt();
}
