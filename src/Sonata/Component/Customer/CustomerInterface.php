<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Customer;

use FOS\UserBundle\Model\UserInterface;

interface CustomerInterface
{
    /**
     * Set createdAt
     *
     * @param \DateTime|null $createdAt
     */
    function setCreatedAt(\DateTime $createdAt = null);

    /**
     * Get createdAt
     *
     * @return \DateTime createdAt
     */
    function getCreatedAt();

    /**
     * Set firstname
     *
     * @param string $firstname
     */
    function setFirstname($firstname);

    /**
     * Get firstname
     *
     * @return string $firstname
     */
    function getFirstname();

    /**
     * Get full name
     *
     * @return string
     */
    function getFullname();

    /**
     * Set lastname
     *
     * @param string $lastname
     */
    function setLastname($lastname);

    /**
     * Get lastname
     *
     * @return string $lastname
     */
    function getLastname();

    /**
     * Set updatedAt
     *
     * @param \DateTime|null $updatedAt
     */
    function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * Get updatedAt
     *
     * @return \DateTime $updatedAt
     */
    function getUpdatedAt();

    /**
     * Set user
     *
     * @param \FOS\UserBundle\Model\UserInterface $user
     */
    function setUser(UserInterface $user);

    /**
     * Get user
     *
     * @return \FOS\UserBundle\Model\UserInterface $user
     */
    function getUser();

    /**
     * Get id
     *
     * @return integer $id
     */
    function getId();


    /**
     * @return void
     */
    function getEmail();

    /**
     * @return string
     */
    function getLocale();

    /**
     * @param string $locale
     * @return void
     */
    function setLocale($locale);
}