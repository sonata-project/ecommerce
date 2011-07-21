<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Customer;

interface AddressInterface
{
    const TYPE_BILLING  = 1;
    const TYPE_DELIVERY = 2;
    const TYPE_CONTACT  = 3;

    function getId();

    /**
     * @abstract
     * @return string return the address name
     */
    function getName();

    /**
     * @abstract
     * @return string return the address (line 1)
     */
    function getAddress1();

    /**
     * @abstract
     * @return string return the address (line 2)
     */
    function getAddress2();

    /**
     * @abstract
     * @return string return the address (line 3)
     */
    function getAddress3();

    /**
     * @abstract
     * @return string return the postcode
     */
    function getPostcode();

    /**
     * @abstract
     * @return string return the city
     */
    function getCity();

    /**
     * @abstract
     * @return string return the ISO country code
     */
    function getCountryCode();

    /**
     * @abstract
     * @return string return the phone number linked to the address
     */
    function getPhone();

}