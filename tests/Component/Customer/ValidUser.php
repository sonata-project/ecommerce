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

namespace Sonata\Component\Tests\Customer;

use Symfony\Component\Security\Core\User\UserInterface;

class ValidUser implements UserInterface
{
    public function getId()
    {
        return 1;
    }

    public function getRoles(): void
    {
    }

    public function getPassword(): void
    {
    }

    public function getSalt(): void
    {
    }

    public function getUsername(): void
    {
    }

    public function eraseCredentials(): void
    {
    }
}
