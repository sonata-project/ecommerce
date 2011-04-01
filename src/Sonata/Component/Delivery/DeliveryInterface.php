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
    function getPrice();

    /**
     * @abstract
     * @return float the vat linked to the delivery
     */
    function getVat();

    /**
     * @abstract
     * @return string the name of the delivery method
     */
    function getName();


    /**
     * @abstract
     * @return boolean return true an address is required to use this delivery method
     */
    function isAddressRequired();

    /**
     * Return the delivery price
     *
     * @abstract
     * @param  $vat set to true if the VAT should be included
     * @return float
     */
    function getTotal($price, $vat = false);

    /**
     * Return the vat amount
     *
     * @abstract
     * @param  $vat set to true if the VAT should be included
     * @return float
     */
    function getVatAmount($basket);

    
}