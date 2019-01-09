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

namespace Sonata\Component\Tests\Delivery;

use PHPUnit\Framework\TestCase;
use Sonata\ProductBundle\Entity\BaseDelivery;

class Delivery extends BaseDelivery
{
}

class BaseDeliveryTest extends TestCase
{
    public function testArrayDelivery(): void
    {
        $delivery = new Delivery();

        $arrayDelivery = [
            'code' => 'code',
            'perItem' => 1,
            'countryCode' => 'FR',
            'zone' => 'zone',
            'enabled' => 1,
        ];

        $delivery->fromArray($arrayDelivery);

        $this->assertEquals($arrayDelivery, $delivery->toArray());

        $this->assertEquals($delivery->getCode(), $arrayDelivery['code']);
        $this->assertEquals($delivery->getPerItem(), $arrayDelivery['perItem']);
        $this->assertEquals($delivery->getCountryCode(), $arrayDelivery['countryCode']);
        $this->assertEquals($delivery->getZone(), $arrayDelivery['zone']);
        $this->assertEquals($delivery->getEnabled(), $arrayDelivery['enabled']);
    }
}
