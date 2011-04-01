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

interface InvoiceInterface
{

    function getId();

    /**
     * Set reference
     *
     * @param string $reference
     */
    function setReference($reference);

    /**
     * Get reference
     *
     * @return string $reference
     */
    function getReference();

    /**
     * Set user_id
     *
     * @param integer $userId
     */
       
    
    function setUserId($userId);

    /**
     * Get user_id
     *
     * @return integer $userId
     */
       
    
    
    
    
    function getUserId();

    /**
     * Set currency
     *
     * @param string $currency
     */
       
    
    
    function setCurrency($currency);

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
    function setStatus($status);

    /**
     * Get status
     *
     * @return integer $status
     */
    function getStatus();

    /**
     * Set totalInc
     *
     * @param decimal $totalInc
     */
    function setTotalInc($totalInc);

    /**
     * Get totalInc
     *
     * @return decimal $totalInc
     */
    function getTotalInc();

    /**
     * Set totalExcl
     *
     * @param decimal $totalExcl
     */
    function setTotalExcl($totalExcl);

    /**
     * Get totalExcl
     *
     * @return decimal $totalExcl
     */
    function getTotalExcl();

    /**
     * Set name
     *
     * @param string $name
     */
    function setName($name);

    /**
     * Get name
     *
     * @return string $name
     */
    function getName();

    /**
     * Set phone
     *
     * @param string $phone
     */
    function setPhone($phone);

    /**
     * Get phone
     *
     * @return string $phone
     */
    function getPhone();

    /**
     * Set address1
     *
     * @param string $address1
     */
    function setAddress1($address1);

    /**
     * Get address1
     *
     * @return string $address1
     */
    function getAddress1();

    /**
     * Set address2
     *
     * @param string $address2
     */
    function setAddress2($address2);

    /**
     * Get address2
     *
     * @return string $address2
     */
    function getAddress2();

    /**
     * Set address3
     *
     * @param string $address3
     */
    function setAddress3($address3);

    /**
     * Get address3
     *
     * @return string $address3
     */
    function getAddress3();

    /**
     * Set city
     *
     * @param string $city
     */
    function setCity($city);

    /**
     * Get city
     *
     * @return string $city
     */
    function getCity();

    /**
     * Set postcode
     *
     * @param string $postcode
     */
    function setPostcode($postcode);

    /**
     * Get postcode
     *
     * @return string $postcode
     */
    function getPostcode();

    /**
     * Set country
     *
     * @param string $country
     */
    function setCountry($country);

    /**
     * Get country
     *
     * @return string $country
     */
    function getCountry();

    /**
     * Set fax
     *
     * @param string $fax
     */
    function setFax($fax);

    /**
     * Get fax
     *
     * @return string $fax
     */
    function getFax();

    /**
     * Set email
     *
     * @param string $email
     */
    function setEmail($email);

    /**
     * Get email
     *
     * @return string $email
     */
    function getEmail();

    /**
     * Set mobile
     *
     * @param string $mobile
     */
    function setMobile($mobile);

    /**
     * Get mobile
     *
     * @return string $mobile
     */
    function getMobile();

    /**
     * Set user
     *
     * @param Application\FOS\UserBundle\Entity\User $user
     */
    function setUser($user);

    /**
     * Get user
     *
     * @return Application\FOS\UserBundle\Entity\User $user
     */
    function getUser();
}