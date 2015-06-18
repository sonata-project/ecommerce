<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Delivery;

use Sonata\Component\Basket\BasketInterface;

interface ServiceDeliveryInterface
{
    const STATUS_OPEN       = 1;    // Not processed yet
    const STATUS_PENDING    = 2;    // Packing
    const STATUS_SENT       = 3;    // In transit
    const STATUS_CANCELLED  = 4;    // Delivery cancelled
    const STATUS_COMPLETED  = 5;    // Delivered
    const STATUS_RETURNED   = 6;    // Returned to sender

    /**
     * @return float the delivery base price
     */
    public function getPrice();

    /**
     * Sets the VAT rate.
     *
     * @param float $vat
     */
    public function setVatRate($vat);

    /**
     * @return float the vat linked to the delivery
     */
    public function getVatRate();

    /**
     * @return string the name of the delivery method
     */
    public function getName();

    /**
     * @return bool return true an address is required to use this delivery method
     */
    public function isAddressRequired();

    /**
     * Return the delivery price.
     *
     * @param \Sonata\Component\Basket\BasketInterface $basket
     * @param bool                                     $vat
     *
     * @return float
     */
    public function getTotal(BasketInterface $basket, $vat = false);

    /**
     * Return the vat amount.
     *
     * @param \Sonata\Component\Basket\BasketInterface $basket
     *
     * @return float
     */
    public function getVatAmount(BasketInterface $basket);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @return bool
     */
    public function getEnabled();

    /**
     * @return int
     */
    public function getPriority();
}
