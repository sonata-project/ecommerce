<?php

namespace Sonata\CustomerBundle\Entity;

use Sonata\Component\Customer\CustomerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Sonata\Component\Customer\AddressInterface;
use FOS\UserBundle\Model\UserInterface;

/**
 * Sonata\CustomerBundle\Entity\BaseCustomer
 */
abstract class BaseCustomer implements CustomerInterface
{
    const TITLE_MLLE = 1;
    const TITLE_MME = 2;
    const TITLE_MR = 3;

    /**
     * @var string $title
     */
    protected $title;

    /**
     * @var string $firstname
     */
    protected $firstname;

    /**
     * @var string $lastname
     */
    protected $lastname;

    /**
     * @var string $email
     */
    protected $email;

    /**
     * @var \DateTime $birthDate
     */
    protected $birthDate;

    /**
     * @var string $birthPlace
     */
    protected $birthPlace;

    /**
     * @var string $phoneNumber
     */
    protected $phoneNumber;

    /**
     * @var string $mobileNumber
     */
    protected $mobileNumber;

    /**
     * @var string $faxNumber
     */
    protected $faxNumber;

    /**
     * @var \DateTime $updatedAt
     */
    protected $updatedAt;

    /**
     * @var \DateTime $createdAt
     */
    protected $createdAt;

    /**
     * @var \FOS\UserBundle\Model\UserInterface $user
     */
    protected $user;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection $addresses
     */
    protected $addresses;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection $orders
     */
    protected $orders;

    /**
     * @var boolean $isFake
     */
    protected $isFake;

    public function __construct()
    {
        $this->title        = self::TITLE_MR;
        $this->addresses    = new ArrayCollection();
        $this->orders       = new ArrayCollection();
        $this->isFake       = false;
    }

    public function __toString()
    {
        return $this->getFullname();
    }

    public function prePersist()
    {
        $this->setCreatedAt(new \DateTime);
        $this->setUpdatedAt(new \DateTime);
    }

    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime);
    }

    public function getAdminTitle()
    {
        return $this->getFullname();
    }

    /**
     * Get title
     *
     * @return integer $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param integer $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @static
     * @return array
     */
    public static function getTitlesList()
    {
        return array(
            self::TITLE_MLLE => 'sonata_customer_title_mlle',
            self::TITLE_MME => 'sonata_customer_title_mme',
            self::TITLE_MR => 'sonata_customer_title_mr',
        );
    }

    /**
     * Get title name
     *
     * @return string
     */
    public function getTitleName()
    {
        $list = self::getTitlesList();

        return isset($list[$this->title]) ? $list[$this->title] : '';
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
     * Set firstname
     *
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
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
     * Set lastname
     *
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * Get full name
     *
     * @return string
     */
    public function getFullname()
    {
        return $this->getFirstname(). ' ' . $this->getLastname();
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
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime $birthDate
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set birthDate
     *
     * @param \DateTime|null $birthDate
     */
    public function setBirthDate(\DateTime $birthDate = null)
    {
        $this->birthDate = $birthDate;
    }

    /**
     * Get birthPlace
     *
     * @return string $birthPlace
     */
    public function getBirthPlace()
    {
        return $this->birthPlace;
    }

    /**
     * Set birthPlace
     *
     * @param string $birthPlace
     */
    public function setBirthPlace($birthPlace)
    {
        $this->birthPlace = $birthPlace;
    }

    /**
     * Get phoneNumber
     *
     * @return string $phoneNumber
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set phoneNumber
     *
     * @param string $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * Get mobileNumber
     *
     * @return string $mobileNumber
     */
    public function getMobileNumber()
    {
        return $this->mobileNumber;
    }

    /**
     * Set mobileNumber
     *
     * @param string $mobileNumber
     */
    public function setMobileNumber($mobileNumber)
    {
        $this->mobileNumber = $mobileNumber;
    }

    /**
     * Get faxNumber
     *
     * @return string $faxNumber
     */
    public function getFaxNumber()
    {
        return $this->faxNumber;
    }

    /**
     * Set faxNumber
     *
     * @param string $faxNumber
     */
    public function setFaxNumber($faxNumber)
    {
        $this->faxNumber = $faxNumber;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime|null $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime|null $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Set user
     *
     * @param \FOS\UserBundle\Model\UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return \FOS\UserBundle\Model\UserInterface $user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add address to addresses
     *
     * @param \Sonata\Component\Customer\AddressInterface $address
     */
    public function addAddress(AddressInterface $address)
    {
        $address->setCustomer($this);

        $this->addresses->add($address);
    }

    /**
     * Get addresses
     *
     * @return \Doctrine\Common\Collections\ArrayCollection $addresses
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Get addresses by type
     *
     * @param integer $type
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAddressesByType($type)
    {
        $addresses = new ArrayCollection();

        foreach ($this->getAddresses() as $address) {
            if ($type == $address->getType()) {
                $addresses->add($address);
            }
        }

        return $addresses;
    }

    /**
     * Set orders
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $orders
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;
    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\ArrayCollection $orders
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * Set isFake
     *
     * @param boolean $isFake
     */
    public function setIsFake($isFake)
    {
        $this->isFake = $isFake;
    }

    /**
     * Get isFake
     *
     * @return boolean $isFake
     */
    public function getIsFake()
    {
        return $this->isFake;
    }
}