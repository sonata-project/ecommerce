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

namespace Sonata\Component\Customer;

use Symfony\Component\Security\Core\User\UserInterface;

interface CustomerInterface
{
    /**
     * @param \DateTime|null $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null);

    /**
     * @return \DateTime createdAt
     */
    public function getCreatedAt();

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname);

    /**
     * @return string
     */
    public function getFirstname();

    /**
     * @return string
     */
    public function getFullname();

    /**
     * @param string $lastname
     */
    public function setLastname($lastname);

    /**
     * @return string
     */
    public function getLastname();

    /**
     * @param \DateTime|null $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user);

    /**
     * @return UserInterface
     */
    public function getUser();

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @return string
     */
    public function getLocale();

    /**
     * @param string $locale
     */
    public function setLocale($locale);

    /**
     * @param int $type
     *
     * @return AddressInterface[]
     */
    public function getAddressesByType($type);

    /**
     * @return AddressInterface[]
     */
    public function getAddresses();
}
