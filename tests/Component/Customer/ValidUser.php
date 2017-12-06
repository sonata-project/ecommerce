<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Tests\Customer;

use Symfony\Component\Security\Core\User\UserInterface;

class ValidUser implements UserInterface
{
    public function getId()
    {
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }
}
