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
    function setCreatedAt(\DateTime $createdAt = null);

    function getCreatedAt();

    function setFirstname($firstname);

    function getFirstname();

    function getFullname();

    function setLastname($lastname);

    function getLastname();

    function setUpdatedAt(\DateTime $updatedAt = null);

    function getUpdatedAt();

    function setUser($user);

    function getUser();

    function __toString();
}