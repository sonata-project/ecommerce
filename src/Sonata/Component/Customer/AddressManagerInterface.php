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

interface AddressManagerInterface
{

    /**
     * Creates an empty medie instance
     *
     * @return Address
     */
    function createAddress();

    /**
     * Deletes a address
     *
     * @param Address $address
     * @return void
     */
    function deleteAddress(AddressInterface $address);

    /**
     * Finds one address by the given criteria
     *
     * @param array $criteria
     * @return AddressInterface
     */
    function findAddressBy(array $criteria);

    /**
     * Returns the address's fully qualified class name
     *
     * @return string
     */
    function getClass();

    /**
     * Updates a address
     *
     * @param Address $address
     * @return void
     */
    function updateAddress(AddressInterface $address);

}