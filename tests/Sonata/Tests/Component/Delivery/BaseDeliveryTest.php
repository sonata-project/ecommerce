<?php
/*
 * This file is part of the Sonata package.
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
        $delivery = new Delivery;

        $arrayDelivery = array(
            'code'        => 'code',
            'perItem'     => 1,
            'countryCode' => 'FR',
            'zone'        => 'zone',
            'enabled'     => 1,
        );

        $delivery->fromArray($arrayDelivery);

        $this->assertEquals($arrayDelivery, $delivery->toArray());

        $this->assertEquals($delivery->getCode(),        $arrayDelivery['code']);
        $this->assertEquals($delivery->getPerItem(),     $arrayDelivery['perItem']);
        $this->assertEquals($delivery->getCountryCode(), $arrayDelivery['countryCode']);
        $this->assertEquals($delivery->getZone(),        $arrayDelivery['zone']);
        $this->assertEquals($delivery->getEnabled(),     $arrayDelivery['enabled']);
    }
}
