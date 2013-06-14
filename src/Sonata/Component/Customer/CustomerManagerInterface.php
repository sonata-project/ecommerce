<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Customer;

interface CustomerManagerInterface
{
    /**
     * Creates an empty customer instance
     *
     * @return Customer
     */
    public function create();

    /**
     * Deletes a customer
     *
     * @param  Customer $customer
     * @return void
     */
    public function delete(CustomerInterface $customer);

    /**
     * Finds one customer by the given criteria
     *
     * @param  array             $criteria
     * @return CustomerInterface
     */
    public function findOneBy(array $criteria);

    /**
     * Finds many customers by the given criteria
     *
     * @param  array             $criteria
     * @return CustomerInterface
     */
    public function findBy(array $criteria);

    /**
     * Returns the customer's fully qualified class name
     *
     * @return string
     */
    public function getClass();

    /**
     * Saves a customer
     *
     * @param  Customer $customer
     * @return void
     */
    public function save(CustomerInterface $customer);
}
