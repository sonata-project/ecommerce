<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sonata\Component\Invoice;

use Sonata\Component\Customer\CustomerInterface;

interface InvoiceInterface
{

    public function getId();

    /**
     * Set reference
     *
     * @param string $reference
     */
    public function setReference($reference);

    /**
     * Get reference
     *
     * @return string $reference
     */
    public function getReference();

    /**
     * Set currency
     *
     * @param string $currency
     */
    public function setCurrency($currency);

    /**
     * Get currency
     *
     * @return string $currency
     */
     function getCurrency();

    /**
     * Set status
     *
     * @param integer $status
     */
    public function setStatus($status);

    /**
     * Get status
     *
     * @return integer $status
     */
    public function getStatus();

    /**
     * Set totalInc
     *
     * @param decimal $totalInc
     */
    public function setTotalInc($totalInc);

    /**
     * Get totalInc
     *
     * @return decimal $totalInc
     */
    public function getTotalInc();

    /**
     * Set totalExcl
     *
     * @param decimal $totalExcl
     */
    public function setTotalExcl($totalExcl);

    /**
     * Get totalExcl
     *
     * @return decimal $totalExcl
     */
    public function getTotalExcl();

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName();

    /**
     * Set phone
     *
     * @param string $phone
     */
    public function setPhone($phone);

    /**
     * Get phone
     *
     * @return string $phone
     */
    public function getPhone();

    /**
     * Set address1
     *
     * @param string $address1
     */
    public function setAddress1($address1);

    /**
     * Get address1
     *
     * @return string $address1
     */
    public function getAddress1();

    /**
     * Set address2
     *
     * @param string $address2
     */
    public function setAddress2($address2);

    /**
     * Get address2
     *
     * @return string $address2
     */
    public function getAddress2();

    /**
     * Set address3
     *
     * @param string $address3
     */
    public function setAddress3($address3);

    /**
     * Get address3
     *
     * @return string $address3
     */
    public function getAddress3();

    /**
     * Set city
     *
     * @param string $city
     */
    public function setCity($city);

    /**
     * Get city
     *
     * @return string $city
     */
    public function getCity();

    /**
     * Set postcode
     *
     * @param string $postcode
     */
    public function setPostcode($postcode);

    /**
     * Get postcode
     *
     * @return string $postcode
     */
    public function getPostcode();

    /**
     * Set country
     *
     * @param string $country
     */
    public function setCountry($country);

    /**
     * Get country
     *
     * @return string $country
     */
    public function getCountry();

    /**
     * Set fax
     *
     * @param string $fax
     */
    public function setFax($fax);

    /**
     * Get fax
     *
     * @return string $fax
     */
    public function getFax();

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email);

    /**
     * Get email
     *
     * @return string $email
     */
    public function getEmail();

    /**
     * Set mobile
     *
     * @param string $mobile
     */
    public function setMobile($mobile);

    /**
     * Get mobile
     *
     * @return string $mobile
     */
    public function getMobile();

    /**
     * Set user
     *
     * @param \Sonata\Component\Customer\CustomerInterface $user
     */
    public function setCustomer(CustomerInterface $customer);

    /**
     * Get user
     *
     * @return Application\Sonata\UserBundle\Entity\User $user
     */
    public function getCustomer();
}
