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
    const STATUS_OPEN       = 1;
    const STATUS_PENDING    = 2;
    const STATUS_SENT       = 3;
    const STATUS_CANCELLED  = 4;
    const STATUS_COMPLETED  = 5;
    const STATUS_RETURNED   = 6;

    /**
     * @return float the delivery base price
     */
    public function getPrice();

    /**
     * @return float the vat linked to the delivery
     */
    public function getVat();

    /**
     * @return string the name of the delivery method
     */
    public function getName();

    /**
     * @return boolean return true an address is required to use this delivery method
     */
    public function isAddressRequired();

    /**
     * Return the delivery price
     *
     * @param  \Sonata\Component\Basket\BasketInterface $basket
     * @param  bool                                     $vat
     *
     * @return float
     */
    public function getTotal(BasketInterface $basket, $vat = false);

    /**
     * Return the vat amount
     *
     * @param  \Sonata\Component\Basket\BasketInterface $basket
     *
     * @return float
     */
    public function getVatAmount(BasketInterface $basket);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @return boolean
     */
    public function getEnabled();

    /**
     * @return integer
     */
    public function getPriority();
}
