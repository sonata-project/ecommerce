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
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getFirstname();

    /**
     * @return string
     */
    public function getLastname();

    /**
     * @return string
     */
    public function getAddress1();

    /**
     * @return string
     */
    public function getAddress2();

    /**
     * @return string
     */
    public function getAddress3();

    /**
     * @return string
     */
    public function getPostcode();

    /**
     * @return string
     */
    public function getCity();

    /**
     * @return string
     */
    public function getCountryCode();

    /**
     * @return string
     */
    public function getPhone();

    /**
     * @return bool
     */
    public function getCurrent();

    /**
     * @param bool $current
     */
    public function setCurrent($current);

    /**
     * @return CustomerInterface
     */
    public function getCustomer();

    /**
     * @param CustomerInterface $customer
     */
    public function setCustomer(CustomerInterface $customer);

    /**
     * @return int
     */
    public function getType();

    /**
     * Returns the HTML string representation of the address.
     *
     * @return string
     */
    public function getFullAddressHtml();
}
