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

use Sonata\Component\Model\TimestampableInterface;

interface DeliveryInterface extends TimestampableInterface
{
    /**
     * Set product
     *
     * @param ProductInterface $product
     */
    public function setProduct(ProductInterface $product);

    /**
     * Get product
     *
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * Set class_name
     *
     * @param string $code
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
     * Set country code
     *
     * @param string $countryCode
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
     * Returns Delivery base data as an array.
     *
     * @return array
     */
    public function toArray();

    /**
     * Populate entity from an array.
     *
     * @param array $array
     */
    public function fromArray($array);
}
