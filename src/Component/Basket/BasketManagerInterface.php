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

namespace Sonata\Component\Basket;

use Sonata\Component\Customer\CustomerInterface;
use Sonata\CoreBundle\Model\ManagerInterface;
use Sonata\CoreBundle\Model\PageableManagerInterface;

interface BasketManagerInterface extends ManagerInterface, PageableManagerInterface
{
    /**
     * @param CustomerInterface $customer
     *
     * @return BasketInterface|null
     */
    public function loadBasketPerCustomer(CustomerInterface $customer);
}
