<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Basket;

interface AddressInterface {

    /**
     * @abstract
     * @return string return the address (line 1)
     */
    public function getAddress1();

    /**
     * @abstract
     * @return string return the address (line 1)
     */
    public function getAddress2();

    /**
     * @abstract
     * @return string return the address (line 1)
     */
    public function getAddress3();

    /**
     * @abstract
     * @return string return the postcode
     */
    public function getPostcode();

    /**
     * @abstract
     * @return string return the city
     */
    public function getCity();

    /**
     * @abstract
     * @return string return the ISO country code
     */
    public function getCountryCode();

    /**
     * @abstract
     * @return string return the phone number linked to the address
     */
    public function getPhone();

}