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

namespace Sonata\Component\Order;

use Sonata\Component\Product\PriceComputableInterface;
use Sonata\Component\Product\ProductInterface;

interface OrderElementInterface extends PriceComputableInterface
{
    /**
     * @param OrderInterface $order
     */
    public function setOrder(OrderInterface $order);

    /**
     * @return OrderInterface
     */
    public function getOrder();

    /**
     * @param string $designation
     */
    public function setDesignation($designation);

    /**
     * @return string
     */
    public function getDesignation();

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param array $options
     */
    public function setOptions($options);

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @param array $rawProduct
     */
    public function setRawProduct($rawProduct);

    /**
     * @return array
     */
    public function getRawProduct();

    /**
     * @param int $status
     */
    public function setStatus($status);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $deliveryStatus
     */
    public function setDeliveryStatus($deliveryStatus);

    /**
     * @return int
     */
    public function getDeliveryStatus();

    /**
     * @param \Datetime|null $validatedAt
     */
    public function setValidatedAt(\DateTime $validatedAt = null);

    /**
     * @return \Datetime
     */
    public function getValidatedAt();

    /**
     * @param ProductInterface $product
     */
    public function setProduct(ProductInterface $product);

    /**
     * @return ProductInterface $product
     */
    public function getProduct();

    /**
     * @return int
     */
    public function getProductId();

    /**
     * @param string $productType
     */
    public function setProductType($productType);

    /**
     * @return string
     */
    public function getProductType();

    /**
     * @param \DateTime|null $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null);

    /**
     * @return \Datetime
     */
    public function getCreatedAt();

    /**
     * @param \Datetime|null $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * @return \Datetime
     */
    public function getUpdatedAt();

    /**
     * Return the total (price * quantity).
     *
     * if $vat = true, return the price with vat
     *
     * @param bool $vat
     *
     * @return float
     */
    public function getTotal($vat = false);
}
