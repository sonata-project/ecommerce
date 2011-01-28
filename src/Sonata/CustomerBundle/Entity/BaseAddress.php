<?php

namespace Sonata\CustomerBundle\Entity;

/**
 * Sonata\BasketBundle\Entity\BaseAddress
 */
abstract class BaseAddress implements \Sonata\Component\Basket\AddressInterface
{
    const TYPE_BILLING  = 1;
    const TYPE_DELIVERY = 2;
    const TYPE_CONTACT  = 3;

    public static function getTypesList()
    {
        return array(
            self::TYPE_BILLING  => 'type_billing',
            self::TYPE_DELIVERY => 'type_delivery',
            self::TYPE_CONTACT  => 'type_contact',
        );
    }

    /**
     * @var boolean $current
     */
    protected $current;

    /**
     * @var string $name
     */
    protected $name;

    
    /**
     * @var integer $type
     */
    protected $type;

    /**
     * @var string $firstname
     */
    protected $firstname;

    /**
     * @var string $lastname
     */
    protected $lastname;

    /**
     * @var string $addr1
     */
    protected $address1;

    /**
     * @var string $addr2
     */
    protected $address2;

    /**
     * @var string $addr3
     */
    protected $address3;

    /**
     * @var string $postcode
     */
    protected $postcode;

    /**
     * @var string $city
     */
    protected $city;

    /**
     * @var string $country
     */
    protected $countryCode;

    /**
     * @var string $phone
     */
    protected $phone;

    /**
     * @var datetime $updated_at
     */
    protected $updatedAt;

    /**
     * @var datetime $created_at
     */
    protected $createdAt;

    /**
     * @var 
     */
    protected $customer;


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

    public function getTypeCode()
    {
        $types = self::getTypesList();

        return isset($types[$this->getType()]) ? $types[$this->getType()] : null;
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
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
    }

    /**
     * Get addr1
     *
     * @return string $addr1
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set addr2
     *
     * @param string $addr2
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
    }

    /**
     * Get addr2
     *
     * @return string $addr2
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set addr3
     *
     * @param string $addr3
     */
    public function setAddress3($address3)
    {
        $this->address3 = $address3;
    }

    /**
     * Get addr3
     *
     * @return string $addr3
     */
    public function getAddress3()
    {
        return $this->address3;
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
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
    }

    /**
     * Get country
     *
     * @return string $country
     */
    public function getCountryCode()
    {
        return $this->countryCode;
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
     * Set updated_at
     *
     * @param datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updated_at
     *
     * @return datetime $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set created_at
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get created_at
     *
     * @return datetime $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setName($name)
    {

        $this->name = $name;
    }

    public function getName()
    {

        return $this->name;
    }

    public function getFullAddress($sep = "\n")
    {

        return sprintf("%s %s, %s, %s, %s (%s)",
            $this->getFirstname(),
            $this->getLastname(),
            $this->getAddress1(),
            $this->getPostcode(),
            $this->getCity(),
            $this->getCountryCode()
        );
    }

    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

}