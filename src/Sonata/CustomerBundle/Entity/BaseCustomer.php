<?php

namespace Sonata\CustomerBundle\Entity;

use Sonata\Component\Customer\CustomerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Sonata\Component\Customer\AddressInterface;

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
     * @var datetime $birthDate
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
     * @var datetime $updatedAt
     */
    protected $updatedAt;

    /**
     * @var datetime $createdAt
     */
    protected $createdAt;

    /**
     * @var Application\SandboxBundle\Entity\User
     */
    protected $user;

    /**
     * @var Collection
     */
    protected $addresses;

    public function __construct()
    {
        $this->title = self::TITLE_MR;
        $this->addresses = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getFirstname().' '.$this->getLastname();
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public static function getTitlesList()
    {
        return array(
            self::TITLE_MLLE => 'sonata_customer_title_mlle',
            self::TITLE_MME => 'sonata_customer_title_mme',
            self::TITLE_MR => 'sonata_customer_title_mr',
        );
    }

    public function getTitleName()
    {
        $list = self::getTitlesList();

        return isset($list[$this->title]) ? $list[$this->title] : '';
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    public function getFullname()
    {
        return $this->getFirstname(). ' ' . $this->getLastname();
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getBirthDate()
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTime $birthDate)
    {
        $this->birthDate = $birthDate;
    }

    public function getBirthPlace()
    {
        return $this->birthPlace;
    }

    public function setBirthPlace($birthPlace)
    {
        $this->birthPlace = $birthPlace;
    }

    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getMobileNumber()
    {
        return $this->mobileNumber;
    }

    public function setMobileNumber($mobileNumber)
    {
        $this->mobileNumber = $mobileNumber;
    }

    public function getFaxNumber()
    {
        return $this->faxNumber;
    }

    public function setFaxNumber($faxNumber)
    {
        $this->faxNumber = $faxNumber;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function addAddress(AddressInterface $address)
    {
        $this->addresses[] = $address;
    }

    public function getAddresses()
    {
        return $this->addresses;
    }

    public function getAddressesByType($type)
    {
        $addresses = array();

        foreach ($this->getAddresses() as $address) {
            if ($type == $address->getType()) {
                $addresses[] = $address;
            }
        }

        return $addresses;
    }
}