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

namespace Sonata\CustomerBundle\Entity;

use Sonata\Component\Customer\AddressInterface;
use Sonata\Component\Customer\CustomerInterface;

abstract class BaseAddress implements AddressInterface
{
    /**
     * @var bool
     */
    protected $current;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var string
     */
    protected $firstname;

    /**
     * @var string
     */
    protected $lastname;

    /**
     * @var string
     */
    protected $address1;

    /**
     * @var string
     */
    protected $address2;

    /**
     * @var string
     */
    protected $address3;

    /**
     * @var string
     */
    protected $postcode;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $countryCode;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var
     */
    protected $customer;

    public function __construct()
    {
        $this->setCurrent(false);
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Formats an address in an array form.
     *
     * @param array  $address The address array (required keys: firstname, lastname, address1, postcode, city, country_code)
     * @param string $sep     The address separator
     *
     * @return string
     */
    public static function formatAddress(array $address, $sep = ', ')
    {
        $address = array_merge(
            [
                'firstname' => '',
                'lastname' => '',
                'address1' => '',
                'address2' => '',
                'address3' => '',
                'postcode' => '',
                'city' => '',
                'country_code' => '',
            ],
            $address
        );

        $values = array_map('trim', [
                sprintf('%s %s', $address['firstname'], $address['lastname']),
                $address['address1'],
                $address['address2'],
                $address['address3'],
                $address['postcode'],
                $address['city'],
            ]);

        foreach ($values as $key => $val) {
            if (!$val) {
                unset($values[$key]);
            }
        }

        $fullAddress = implode($sep, $values);

        if ($countryCode = trim($address['country_code'])) {
            if ($fullAddress) {
                $fullAddress .= ' ';
            }

            $fullAddress .= sprintf('(%s)', $countryCode);
        }

        return $fullAddress;
    }

    public function prePersist(): void
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    public function preUpdate(): void
    {
        $this->setUpdatedAt(new \DateTime());
    }

    public static function getTypesList()
    {
        return [
            self::TYPE_BILLING => 'type_billing',
            self::TYPE_DELIVERY => 'type_delivery',
            self::TYPE_CONTACT => 'type_contact',
        ];
    }

    /**
     * Set current.
     *
     * @param bool $current
     */
    public function setCurrent($current): void
    {
        $this->current = $current;
    }

    /**
     * Get current.
     *
     * @return bool
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * Set type.
     *
     * @param int $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    public function getTypeCode()
    {
        $types = self::getTypesList();

        return isset($types[$this->getType()]) ? $types[$this->getType()] : null;
    }

    /**
     * Get type.
     *
     * @return int $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set firstname.
     *
     * @param string $firstname
     */
    public function setFirstname($firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * Get firstname.
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname.
     *
     * @param string $lastname
     */
    public function setLastname($lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * Get lastname.
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set address1.
     *
     * @param string $address1
     */
    public function setAddress1($address1): void
    {
        $this->address1 = $address1;
    }

    /**
     * Get address1.
     *
     * @return string
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set address2.
     *
     * @param string $address2
     */
    public function setAddress2($address2): void
    {
        $this->address2 = $address2;
    }

    /**
     * Get address2.
     *
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set address3.
     *
     * @param string $address3
     */
    public function setAddress3($address3): void
    {
        $this->address3 = $address3;
    }

    /**
     * Get address3.
     *
     * @return string
     */
    public function getAddress3()
    {
        return $this->address3;
    }

    /**
     * Set postcode.
     *
     * @param string $postcode
     */
    public function setPostcode($postcode): void
    {
        $this->postcode = $postcode;
    }

    /**
     * Get postcode.
     *
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Set city.
     *
     * @param string $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

    /**
     * Get city.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set countryCode.
     *
     * @param string $countryCode
     */
    public function setCountryCode($countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    /**
     * Get countryCode.
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Set phone.
     *
     * @param string $phone
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    /**
     * Get phone.
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set updatedAt.
     *
     * @param \Datetime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updatedAt.
     *
     * @return \Datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set createdAt.
     *
     * @param \Datetime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt.
     *
     * @return \Datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $sep
     *
     * @return string
     */
    public function getFullAddress($sep = ', ')
    {
        return self::formatAddress($this->getAddressArrayForRender(), $sep);
    }

    /**
     * @return array
     */
    public function getAddressArrayForRender()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'firstname' => $this->getFirstName(),
            'lastname' => $this->getLastname(),
            'address1' => $this->getAddress1(),
            'address2' => $this->getAddress2(),
            'address3' => $this->getAddress3(),
            'postcode' => $this->getPostcode(),
            'city' => $this->getCity(),
            'country_code' => $this->getCountryCode(),
        ];
    }

    /**
     * @return string
     */
    public function getFullAddressHtml()
    {
        return $this->getFullAddress('<br/>');
    }

    public function setCustomer(CustomerInterface $customer): void
    {
        $this->customer = $customer;
    }

    public function getCustomer()
    {
        return $this->customer;
    }
}
