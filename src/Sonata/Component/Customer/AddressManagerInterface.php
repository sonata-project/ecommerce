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
    public function create();

    /**
     * Deletes a address
     *
     * @param  Address $address
     * @return void
     */
    public function delete(AddressInterface $address);

    /**
     * Finds one address by the given criteria
     *
     * @param  array $criteria
     * @return array
     */
    public function findBy(array $criteria);

    /**
     * Finds one address by the given criteria
     *
     * @param  array            $criteria
     * @return AddressInterface
     */
    public function findOneBy(array $criteria);

    /**
     * Returns the address's fully qualified class name
     *
     * @return string
     */
    public function getClass();

    /**
     * Saves an address
     *
     * @param  Address $address
     * @return void
     */
    public function save(AddressInterface $address);
}
