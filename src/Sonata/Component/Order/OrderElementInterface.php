<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Order;

use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Order\OrderInterface;


interface OrderElementInterface
{

    /**
     * Set order
     *
     * @param OrderInterface $order
     */
    function setOrder(OrderInterface $order);

    /**
     * Get order
     *
     * @return Order $order
     */
    function getOrder();

    /**
     * Set quantity
     *
     * @param integer $quantity
     */
    function setQuantity($quantity);

    /**
     * Get quantity
     *
     * @return integer $quantity
     */
    function getQuantity();

    /**
     * Set price
     *
     * @param decimal $price
     */
    function setPrice($price);

    /**
     * Get price
     *
     * @return decimal $price
     */
    function getPrice();

    /**
     * Set vat
     *
     * @param decimal $vat
     */
    function setVat($vat);

    /**
     * Get vat
     *
     * @return decimal $vat
     */
    function getVat();

    /**
     * Set designation
     *
     * @param string $designation
     */
    function setDesignation($designation);

    /**
     * Get designation
     *
     * @return string $designation
     */
    function getDesignation();

    /**
     * Set description
     *
     * @param text $description
     */
    function setDescription($description);

    /**
     * Get description
     *
     * @return text $description
     */
    function getDescription();

    /**
     * Set serialize
     *
     * @param text $serialize
     */
    function setSerialize($serialize);

    /**
     * Get serialize
     *
     * @return text $serialize
     */
    function getSerialize();

    /**
     * Set status
     *
     * @param integer $status
     */
    function setStatus($status);

    /**
     * Get status
     *
     * @return integer $status
     */
    function getStatus();

    /**
     * Set delivery_status
     *
     * @param integer $deliveryStatus
     */
    function setDeliveryStatus($deliveryStatus);

    /**
     * Get delivery_status
     *
     * @return integer $deliveryStatus
     */
    function getDeliveryStatus();

    /**
     * Set validated_at
     *
     * @param datetime $validatedAt
     */
    function setValidatedAt(\DateTime $validatedAt = null);

    /**
     * Get validated_at
     *
     * @return datetime $validatedAt
     */
    function getValidatedAt();

    /**
     * Add product
     *
     * @param ProductInterface $product
     */
    function setProduct(ProductInterface $product);

    /**
     * Get product
     *
     * @return ProductInterface $product
     */
    function getProduct();

    /**
     * @abstract
     * @return void
     */
    function getProductId();

    /**
     * Set product_type
     *
     * @param string $productType
     */
    function setProductType($productType);

    /**
     * Get product_type
     *
     * @return string $productType
     */
    function getProductType();

    /**
     * @abstract
     * @param \DateTime|null $createdAt
     * @return void
     */
    function setCreatedAt(\DateTime $createdAt = null);

    /**
     * @abstract
     * @return DateTime
     */
    function getCreatedAt();

    /**
     * @abstract
     * @param \DateTime|null $updatedAt
     * @return void
     */
    function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * @abstract
     * @return DateTime
     */
    function getUpdatedAt();
}