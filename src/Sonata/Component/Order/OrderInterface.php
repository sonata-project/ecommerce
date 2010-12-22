<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Order;

interface OrderInterface
{


    const STATUS_OPEN       = 0; // created but not validated
    const STATUS_PENDING    = 1; // waiting from action from the user
    const STATUS_VALIDATED  = 2; // the order is validated does not mean the payment is ok
    const STATUS_CANCELLED  = 3; // the order is cancelled
    const STATUS_ERROR      = 4; // the order has an error
    const STATUS_STOPPED    = 5; // use if the subscription has been cancelled/stopped


    public function setStatus($status);

    public function getStatus();
}