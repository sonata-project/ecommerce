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

namespace Sonata\Component\Delivery;

use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Customer\AddressInterface;

interface ServiceDeliverySelectorInterface
{
    /**
     * @param null|\Sonata\Component\Basket\BasketInterface    $basket
     * @param null|\Sonata\Component\Customer\AddressInterface $deliveryAddress
     */
    public function getAvailableMethods(BasketInterface $basket = null, AddressInterface $deliveryAddress = null);
}
