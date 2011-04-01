<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Delivery;

/**
 * A free delivery method, used this only for testing
 *
 */
class FreeDelivery extends BaseDelivery
{

    public function getVat()
    {
        return 0;
    }

    public function getPrice()
    {
        return 0;
    }

    public function isAddressRequired()
    {
        return true;
    }

    public function getName()
    {
        return 'Free delivery';
    }
    
}