<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Customer;

use Sonata\CoreBundle\Model\ManagerInterface;

interface AddressManagerInterface extends ManagerInterface
{
    /**
     * Sets $address the current customer address.
     *
     * @param AddressInterface $address
     */
    public function setCurrent(AddressInterface $address);
}
