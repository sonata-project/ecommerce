<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\Component\Delivery;

use Sonata\ProductBundle\Entity\BaseDelivery;

class Delivery extends BaseDelivery
{
}

class BaseDeliveryTest extends \PHPUnit_Framework_TestCase
{
    public function testArrayDelivery()
    {
        $delivery = new Delivery();

        $arrayDelivery = array(
            'code'        => 'code',
            'perItem'     => 1,
            'countryCode' => 'FR',
            'zone'        => 'zone',
            'enabled'     => 1,
        );

        $delivery->fromArray($arrayDelivery);

        $this->assertSame($arrayDelivery, $delivery->toArray());

        $this->assertSame($delivery->getCode(),        $arrayDelivery['code']);
        $this->assertSame($delivery->getPerItem(),     $arrayDelivery['perItem']);
        $this->assertSame($delivery->getCountryCode(), $arrayDelivery['countryCode']);
        $this->assertSame($delivery->getZone(),        $arrayDelivery['zone']);
        $this->assertSame($delivery->getEnabled(),     $arrayDelivery['enabled']);
    }
}
