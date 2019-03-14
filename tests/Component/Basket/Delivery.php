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

namespace Sonata\Component\Tests\Basket;

use Sonata\Component\Delivery\BaseServiceDelivery;

class Delivery extends BaseServiceDelivery
{
    public function isAddressRequired()
    {
        return true;
    }

    public function getName()
    {
        return 'delivery 1';
    }

    public function getVatRate()
    {
        return 19.6;
    }

    public function getPrice()
    {
        return 120;
    }
}
