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


interface CustomerInterface
{
    /**
     * @abstract
     * @param \DateTime|null $createdAt
     * @return void
     */
    function setCreatedAt(\DateTime $createdAt = null);

    /**
     * @abstract
     * @return \DateTime
     */
    function getCreatedAt();

    /**
     * @abstract
     * @param string $firstname
     * @return void
     */
    function setFirstname($firstname);

    /**
     * @abstract
     * @return void
     */
    function getFirstname();

    /**
     * @abstract
     * @return void
     */
    function getFullname();

    /**
     * @abstract
     * @param $lastname
     * @return void
     */
    function setLastname($lastname);

    /**
     * @abstract
     * @return void
     */
    function getLastname();

    /**
     * @abstract
     * @param \DateTime|null $updatedAt
     * @return void
     */
    function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * @abstract
     * @return \DateTime
     */
    function getUpdatedAt();

    /**
     * @abstract
     * @param $user
     * @return void
     */
    function setUser($user);

    /**
     * @abstract
     * @return void
     */
    function getUser();

    /**
     * @abstract
     * @return void
     */
    function getId();
}