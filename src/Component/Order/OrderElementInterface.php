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
     * Set order.
     *
     * @param \Sonata\Component\Order\OrderInterface $order
     */
    public function setOrder(OrderInterface $order);

    /**
     * Get order.
     *
     * @return \Sonata\Component\Order\OrderInterface
     */
    public function getOrder();

    /**
     * Set designation.
     *
     * @param string $designation
     */
    public function setDesignation($designation);

    /**
     * Get designation.
     *
     * @return string $designation
     */
    public function getDesignation();

    /**
     * Set description.
     *
     * @param string $description
     */
    public function setDescription($description);

    /**
     * Get description.
     *
     * @return string $description
     */
    public function getDescription();

    /**
     * Set serialize.
     *
     * @param string $options
     */
    public function setOptions($options);

    /**
     * Get serialize.
     *
     * @return string $options
     */
    public function getOptions();

    /**
     * @param string $rawProduct
     */
    public function setRawProduct($rawProduct);

    /**
     * @return string
     */
    public function getRawProduct();

    /**
     * Set status.
     *
     * @param int $status
     */
    public function setStatus($status);

    /**
     * Get status.
     *
     * @return int $status
     */
    public function getStatus();

    /**
     * Set delivery_status.
     *
     * @param int $deliveryStatus
     */
    public function setDeliveryStatus($deliveryStatus);

    /**
     * Get delivery_status.
     *
     * @return int $deliveryStatus
     */
    public function getDeliveryStatus();

    /**
     * Set validated_at.
     *
     * @param \Datetime $validatedAt
     */
    public function setValidatedAt(\DateTime $validatedAt = null);

    /**
     * Get validated_at.
     *
     * @return \Datetime $validatedAt
     */
    public function getValidatedAt();

    /**
     * Add product.
     *
     * @param ProductInterface $product
     */
    public function setProduct(ProductInterface $product);

    /**
     * Get product.
     *
     * @return ProductInterface $product
     */
    public function getProduct();

    /**
     * @return int
     */
    public function getProductId();

    /**
     * Set product_type.
     *
     * @param string $productType
     */
    public function setProductType($productType);

    /**
     * Get product_type.
     *
     * @return string $productType
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
     * @param \Datetime
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
