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
     * Set createdAt.
     *
     * @param \DateTime|null $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null);

    /**
     * Get createdAt.
     *
     * @return \DateTime createdAt
     */
    public function getCreatedAt();

    /**
     * Set firstname.
     *
     * @param string $firstname
     */
    public function setFirstname($firstname);

    /**
     * Get firstname.
     *
     * @return string $firstname
     */
    public function getFirstname();

    /**
     * Get full name.
     *
     * @return string
     */
    public function getFullname();

    /**
     * Set lastname.
     *
     * @param string $lastname
     */
    public function setLastname($lastname);

    /**
     * Get lastname.
     *
     * @return string $lastname
     */
    public function getLastname();

    /**
     * Set updatedAt.
     *
     * @param \DateTime|null $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * Get updatedAt.
     *
     * @return \DateTime $updatedAt
     */
    public function getUpdatedAt();

    /**
     * Set user.
     *
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user);

    /**
     * Get user.
     *
     * @return UserInterface $user
     */
    public function getUser();

    /**
     * Get id.
     *
     * @return int $id
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
     * @param $type
     *
     * @return array
     */
    public function getAddressesByType($type);

    /**
     * @return array
     */
    public function getAddresses();
}
