<?php

namespace Sonata\Bundle\BasketBundle\Entity;

/**
 * Sonata\Bundle\BasketBundle\Entity\Address
 */
class Address
{
    /**
     * @var integer $user_id
     */
    private $user_id;

    /**
     * @var boolean $current
     */
    private $current;

    /**
     * @var integer $type
     */
    private $type;

    /**
     * @var string $firstname
     */
    private $firstname;

    /**
     * @var string $lastname
     */
    private $lastname;

    /**
     * @var string $addr1
     */
    private $addr1;

    /**
     * @var string $addr2
     */
    private $addr2;

    /**
     * @var string $addr3
     */
    private $addr3;

    /**
     * @var string $postcode
     */
    private $postcode;

    /**
     * @var string $city
     */
    private $city;

    /**
     * @var string $country
     */
    private $country;

    /**
     * @var string $phone
     */
    private $phone;

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var sfGuardUser
     */
    private $User;

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
     * Set current
     *
     * @param boolean $current
     */
    public function setCurrent($current)
    {
        $this->current = $current;
    }

    /**
     * Get current
     *
     * @return boolean $current
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * Set type
     *
     * @param integer $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return integer $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * Get firstname
     *
     * @return string $firstname
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * Get lastname
     *
     * @return string $lastname
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set addr1
     *
     * @param string $addr1
     */
    public function setAddr1($addr1)
    {
        $this->addr1 = $addr1;
    }

    /**
     * Get addr1
     *
     * @return string $addr1
     */
    public function getAddr1()
    {
        return $this->addr1;
    }

    /**
     * Set addr2
     *
     * @param string $addr2
     */
    public function setAddr2($addr2)
    {
        $this->addr2 = $addr2;
    }

    /**
     * Get addr2
     *
     * @return string $addr2
     */
    public function getAddr2()
    {
        return $this->addr2;
    }

    /**
     * Set addr3
     *
     * @param string $addr3
     */
    public function setAddr3($addr3)
    {
        $this->addr3 = $addr3;
    }

    /**
     * Get addr3
     *
     * @return string $addr3
     */
    public function getAddr3()
    {
        return $this->addr3;
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
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add User
     *
     * @param sfGuardUser $user
     */
    public function addUser(\sfGuardUser $user)
    {
        $this->User[] = $user;
    }

    /**
     * Get User
     *
     * @return Doctrine\Common\Collections\Collection $user
     */
    public function getUser()
    {
        return $this->User;
    }



































































































































































































































}