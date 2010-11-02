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

interface DeliveryInterface {

    const
        STATUS_OPEN      = 0,
        STATUS_SENT      = 1,
        STATUS_CANCELLED = 2,
        STATUS_COMPLETED = 3,
        STATUS_RETURNED  = 4;

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
     * Return the delivery price for the current basket
     *
     * @abstract
     * @param  $basket Basket
     * @param  $vat set to true if the VAT should be included
     * @return float
     */
    public function getDeliveryPrice($basket, $vat = false);
    
}