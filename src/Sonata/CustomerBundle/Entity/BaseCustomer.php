<?php

namespace Sonata\CustomerBundle\Entity;

use Sonata\Component\Customer\CustomerInterface;

/**
 * Sonata\BasketBundle\Entity\BaseAddress
 */
abstract class BaseCustomer implements CustomerInterface
{

    /**
     * @var string $firstname
     */
    protected $firstname;

    /**
     * @var string $lastname
     */
    protected $lastname;


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


    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function getFullname()
    {
        return $this->getFirstname(). ' ' . $this->getLastname();
    }
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function __toString()
    {
        return $this->getFirstname().' '.$this->getLastname();
    }
}