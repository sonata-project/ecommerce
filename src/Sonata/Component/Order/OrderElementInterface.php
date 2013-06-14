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
     * @param \Sonata\Component\Order\OrderInterface $order
     */
    public function setOrder(OrderInterface $order);

    /**
     * Get order
     *
     * @return \Sonata\Component\Order\OrderInterface
     */
    public function getOrder();

    /**
     * Set quantity
     *
     * @param integer $quantity
     */
    public function setQuantity($quantity);

    /**
     * Get quantity
     *
     * @return integer $quantity
     */
    public function getQuantity();

    /**
     * Set price
     *
     * @param decimal $price
     */
    public function setPrice($price);

    /**
     * Get price
     *
     * @return decimal $price
     */
    public function getPrice();

    /**
     * Set vat
     *
     * @param decimal $vat
     */
    public function setVat($vat);

    /**
     * Get vat
     *
     * @return decimal $vat
     */
    public function getVat();

    /**
     * Set designation
     *
     * @param string $designation
     */
    public function setDesignation($designation);

    /**
     * Get designation
     *
     * @return string $designation
     */
    public function getDesignation();

    /**
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description);

    /**
     * Get description
     *
     * @return text $description
     */
    public function getDescription();

    /**
     * Set serialize
     *
     * @param text $options
     */
    public function setOptions($options);

    /**
     * Get serialize
     *
     * @return text $options
     */
    public function getOptions();

    /**
     * @abstract
     * @param $rawProduct
     * @return void
     */
    public function setRawProduct($rawProduct);

    /**
     * @abstract
     * @return void
     */
    public function getRawProduct();

    /**
     * Set status
     *
     * @param integer $status
     */
    public function setStatus($status);

    /**
     * Get status
     *
     * @return integer $status
     */
    public function getStatus();

    /**
     * Set delivery_status
     *
     * @param integer $deliveryStatus
     */
    public function setDeliveryStatus($deliveryStatus);

    /**
     * Get delivery_status
     *
     * @return integer $deliveryStatus
     */
    public function getDeliveryStatus();

    /**
     * Set validated_at
     *
     * @param datetime $validatedAt
     */
    public function setValidatedAt(\DateTime $validatedAt = null);

    /**
     * Get validated_at
     *
     * @return datetime $validatedAt
     */
    public function getValidatedAt();

    /**
     * Add product
     *
     * @param ProductInterface $product
     */
    public function setProduct(ProductInterface $product);

    /**
     * Get product
     *
     * @return ProductInterface $product
     */
    public function getProduct();

    /**
     * @abstract
     * @return void
     */
    public function getProductId();

    /**
     * Set product_type
     *
     * @param string $productType
     */
    public function setProductType($productType);

    /**
     * Get product_type
     *
     * @return string $productType
     */
    public function getProductType();

    /**
     * @abstract
     * @param  \DateTime|null $createdAt
     * @return void
     */
    public function setCreatedAt(\DateTime $createdAt = null);

    /**
     * @abstract
     * @return DateTime
     */
    public function getCreatedAt();

    /**
     * @abstract
     * @param  \DateTime|null $updatedAt
     * @return void
     */
    public function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * @abstract
     * @return DateTime
     */
    public function getUpdatedAt();
}
