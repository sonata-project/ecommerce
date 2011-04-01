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

    /**
     * Set productId
     *
     * @param integer $productId
     */
    function setProduct(ProductInterface $product);

    /**
     * Get productId
     *
     * @return integer $productId
     */
    function getProduct();

    /**
     * Set class_name
     *
     * @param string $className
     */
    function setCode($code);

    /**
     * Get class_name
     *
     * @return string $className
     */
    function getCode();

    /**
     * Set per_item
     *
     * @param boolean $perItem
     */
    function setPerItem($perItem);

    /**
     * Get per_item
     *
     * @return boolean $perItem
     */
    function getPerItem();

    /**
     * Set country
     *
     * @param string $country
     */
    function setCountryCode($countryCode);

    /**
     * Get country
     *
     * @return string $country
     */
    function getCountryCode();

    /**
     * Set zone
     *
     * @param string $zone
     */
    function setZone($zone);

    /**
     * Get zone
     *
     * @return string $zone
     */
    function getZone();

    /**
     * Set enabled
     *
     * @param boolean $enabled
     */
    function setEnabled($enabled);

    /**
     * Get enabled
     *
     * @return boolean $enabled
     */
    function getEnabled();

    /**
     * Set updatedAt
     *
     * @param datetime $updatedAt
     */
    function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * Get updatedAt
     *
     * @return datetime $updatedAt
     */
    function getUpdatedAt();

    /**
     * Set createdAt
     *
     * @param datetime $createdAt
     */
    function setCreatedAt(\DateTime $createdAt = null);

    /**
     * Get createdAt
     *
     * @return datetime $createdAt
     */
    function getCreatedAt();

}