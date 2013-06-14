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

interface DeliveryInterface
{
    const STATUS_OPEN       = 1;
    const STATUS_SENT       = 2;
    const STATUS_CANCELLED  = 3;
    const STATUS_COMPLETED  = 4;
    const STATUS_RETURNED   = 5;

    /**
     * @abstract
     * @return float the delivery base price
     */
    public function getPrice();

    /**
     * @abstract
     * @return float the vat linked to the delivery
     */
    public function getVat();

    /**
     * @abstract
     * @return string the name of the delivery method
     */
    public function getName();

    /**
     * @abstract
     * @return boolean return true an address is required to use this delivery method
     */
    public function isAddressRequired();

    /**
     * Return the delivery price
     *
     * @abstract
     * @param  \Sonata\Component\Basket\BasketInterface $basket
     * @param  bool                                     $vat
     * @return void
     */
    public function getTotal(BasketInterface $basket, $vat = false);

    /**
     * Return the vat amount
     *
     * @abstract
     * @param  \Sonata\Component\Basket\BasketInterface $basket
     * @return void
     */
    public function getVatAmount(BasketInterface $basket);

    /**
     * @abstract
     * @return string
     */
    public function getCode();

    /**
     * @abstract
     * @return boolean
     */
    public function getEnabled();

    /**
     * @abstract
     * @return boolean
     */
    public function getPriority();
}
