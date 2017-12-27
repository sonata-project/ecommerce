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

interface DeliveryInterface
{
    /**
     * @param ProductInterface $product
     */
    public function setProduct(ProductInterface $product);

    /**
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * @param string $code
     */
    public function setCode($code);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param bool $perItem
     */
    public function setPerItem($perItem);

    /**
     * @return bool
     */
    public function getPerItem();

    /**
     * @param string $countryCode
     */
    public function setCountryCode($countryCode);

    /**
     * @return string
     */
    public function getCountryCode();

    /**
     * @param string $zone
     */
    public function setZone($zone);

    /**
     * @return string
     */
    public function getZone();

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled);

    /**
     * @return bool
     */
    public function getEnabled();

    /**
     * @param \Datetime|null $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * @return \Datetime
     */
    public function getUpdatedAt();

    /**
     * @param \Datetime|null $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null);

    /**
     * @return \Datetime
     */
    public function getCreatedAt();

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
