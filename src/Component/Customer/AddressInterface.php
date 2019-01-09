<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Customer;

interface AddressInterface
{
    public const TYPE_BILLING = 1;
    public const TYPE_DELIVERY = 2;
    public const TYPE_CONTACT = 3;

    public function getId();

    /**
     * @return string return the address name
     */
    public function getName();

    /**
     * @return string return the address firstname
     */
    public function getFirstname();

    /**
     * @return string return the address lastname
     */
    public function getLastname();

    /**
     * @return string return the address (line 1)
     */
    public function getAddress1();

    /**
     * @return string return the address (line 2)
     */
    public function getAddress2();

    /**
     * @return string return the address (line 3)
     */
    public function getAddress3();

    /**
     * @return string return the postcode
     */
    public function getPostcode();

    /**
     * @return string return the city
     */
    public function getCity();

    /**
     * @return string return the ISO country code
     */
    public function getCountryCode();

    /**
     * @return string return the phone number linked to the address
     */
    public function getPhone();

    /**
     * @return bool Is it the current address?
     */
    public function getCurrent();

    /**
     * Sets if this address is the current.
     *
     * @param bool $current
     */
    public function setCurrent($current);

    /**
     * Gets address' customer.
     *
     * @return CustomerInterface
     */
    public function getCustomer();

    /**
     * Sets address' customer.
     *
     * @param CustomerInterface $customer
     */
    public function setCustomer(CustomerInterface $customer);

    /**
     * @return int Address' type
     */
    public function getType();

    /**
     * Returns the HTML string representation of the address.
     *
     * @return string
     */
    public function getFullAddressHtml();
}
