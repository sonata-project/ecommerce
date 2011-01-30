<?php

namespace Sonata\InvoiceBundle\Entity;

/**
 * Sonata\InvoiceBundle\Entity\BaseInvoice
 */
abstract class BaseInvoice
{
    /**
     * @var string $reference
     */
    protected $reference;

    /**
     * @var integer $user_id
     */
    protected $customerId;

    /**
     * @var string $currency
     */
    protected $currency;

    /**
     * @var integer $status
     */
    protected $status;

    /**
     * @var decimal $totalInc
     */
    protected $totalInc;

    /**
     * @var decimal $totalExcl
     */
    protected $totalExcl;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $phone
     */
    protected $phone;

    /**
     * @var string $address1
     */
    protected $address1;

    /**
     * @var string $address2
     */
    protected $address2;

    /**
     * @var string $address3
     */
    protected $address3;

    /**
     * @var string $city
     */
    protected $city;

    /**
     * @var string $postcode
     */
    protected $postcode;

    /**
     * @var string $country
     */
    protected $country;

    /**
     * @var string $fax
     */
    protected $fax;

    /**
     * @var string $email
     */
    protected $email;

    /**
     * @var string $mobile
     */
    protected $mobile;

    /**
     * Set reference
     *
     * @param string $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * Get reference
     *
     * @return string $reference
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set user_id
     *
     * @param integer $userId
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;
    }

    /**
     * Get user_id
     *
     * @return integer $userId
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set currency
     *
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * Get currency
     *
     * @return string $currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set status
     *
     * @param integer $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return integer $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set totalInc
     *
     * @param decimal $totalInc
     */
    public function setTotalInc($totalInc)
    {
        $this->totalInc = $totalInc;
    }

    /**
     * Get totalInc
     *
     * @return decimal $totalInc
     */
    public function getTotalInc()
    {
        return $this->totalInc;
    }

    /**
     * Set totalExcl
     *
     * @param decimal $totalExcl
     */
    public function setTotalExcl($totalExcl)
    {
        $this->totalExcl = $totalExcl;
    }

    /**
     * Get totalExcl
     *
     * @return decimal $totalExcl
     */
    public function getTotalExcl()
    {
        return $this->totalExcl;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set phone
     *
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * Get phone
     *
     * @return string $phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set address1
     *
     * @param string $address1
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
    }

    /**
     * Get address1
     *
     * @return string $address1
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set address2
     *
     * @param string $address2
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
    }

    /**
     * Get address2
     *
     * @return string $address2
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set address3
     *
     * @param string $address3
     */
    public function setAddress3($address3)
    {
        $this->address3 = $address3;
    }

    /**
     * Get address3
     *
     * @return string $address3
     */
    public function getAddress3()
    {
        return $this->address3;
    }

    /**
     * Set city
     *
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Get city
     *
     * @return string $city
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set postcode
     *
     * @param string $postcode
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;
    }

    /**
     * Get postcode
     *
     * @return string $postcode
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Set country
     *
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * Get country
     *
     * @return string $country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set fax
     *
     * @param string $fax
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
    }

    /**
     * Get fax
     *
     * @return string $fax
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * Get mobile
     *
     * @return string $mobile
     */
    public function getMobile()
    {
        return $this->mobile;
    }
    /**
     * @var Application\FOS\UserBundle\Entity\User
     */
    protected $user;

    /**
     * Set user
     *
     * @param Application\FOS\UserBundle\Entity\User $user
     */
    public function setUser(\Application\FOS\UserBundle\Entity\User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return Application\FOS\UserBundle\Entity\User $user
     */
    public function getUser()
    {
        return $this->user;
    }
}