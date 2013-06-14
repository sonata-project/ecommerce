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

interface DeliveryInterface
{
    const STATUS_OPEN       = 0; // created but not validated
    const STATUS_PENDING    = 1; // waiting from action from the user
    const STATUS_VALIDATED  = 2; // the order is validated does not mean the payment is ok
    const STATUS_CANCELLED  = 3; // the order is cancelled
    const STATUS_ERROR      = 4; // the order has an error
    const STATUS_STOPPED    = 5; // use if the subscription has been cancelled/stopped

    /**
     * Set productId
     *
     * @param integer $productId
     */
    public function setProduct(ProductInterface $product);

    /**
     * Get productId
     *
     * @return integer $productId
     */
    public function getProduct();

    /**
     * Set class_name
     *
     * @param string $className
     */
    public function setCode($code);

    /**
     * Get class_name
     *
     * @return string $className
     */
    public function getCode();

    /**
     * Set per_item
     *
     * @param boolean $perItem
     */
    public function setPerItem($perItem);

    /**
     * Get per_item
     *
     * @return boolean $perItem
     */
    public function getPerItem();

    /**
     * Set country
     *
     * @param string $country
     */
    public function setCountryCode($countryCode);

    /**
     * Get country
     *
     * @return string $country
     */
    public function getCountryCode();

    /**
     * Set zone
     *
     * @param string $zone
     */
    public function setZone($zone);

    /**
     * Get zone
     *
     * @return string $zone
     */
    public function getZone();

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
