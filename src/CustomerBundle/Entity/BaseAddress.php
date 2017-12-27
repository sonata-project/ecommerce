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
     * @var CustomerInterface
     */
    protected $customer;

    public function __construct()
    {
        $this->setCurrent(false);
    }

    /**
     * @return string
     */
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

    /**
     * @return array
     */
    public static function getTypesList()
    {
        return [
            self::TYPE_BILLING => 'type_billing',
            self::TYPE_DELIVERY => 'type_delivery',
            self::TYPE_CONTACT => 'type_contact',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrent($current): void
    {
        $this->current = $current;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * @param int $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return string|null
     */
    public function getTypeCode()
    {
        $types = self::getTypesList();

        return isset($types[$this->getType()]) ? $types[$this->getType()] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname($lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $address1
     */
    public function setAddress1($address1): void
    {
        $this->address1 = $address1;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * @param string $address2
     */
    public function setAddress2($address2): void
    {
        $this->address2 = $address2;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * @param string $address3
     */
    public function setAddress3($address3): void
    {
        $this->address3 = $address3;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress3()
    {
        return $this->address3;
    }

    /**
     * @param string $postcode
     */
    public function setPostcode($postcode): void
    {
        $this->postcode = $postcode;
    }

    /**
     * {@inheritdoc}
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * @param string $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

    /**
     * {@inheritdoc}
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $countryCode
     */
    public function setCountryCode($countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    /**
     * {@inheritdoc}
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param \Datetime|null $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \Datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \Datetime|null $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \Datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function setCustomer(CustomerInterface $customer): void
    {
        $this->customer = $customer;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomer()
    {
        return $this->customer;
    }
}
