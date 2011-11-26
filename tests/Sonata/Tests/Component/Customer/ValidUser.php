<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\Component\Customer;

use FOS\UserBundle\Model\User as AbstractUser;

// clean this by adding a dedicated interface into the SonataUserBundle
class ValidUser extends AbstractUser
{
    public function getId()
    {
        return 1;
    }

}
