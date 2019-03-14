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

        $this->assertSame($arrayDelivery, $delivery->toArray());

        $this->assertSame($delivery->getCode(), $arrayDelivery['code']);
        $this->assertSame($delivery->getPerItem(), $arrayDelivery['perItem']);
        $this->assertSame($delivery->getCountryCode(), $arrayDelivery['countryCode']);
        $this->assertSame($delivery->getZone(), $arrayDelivery['zone']);
        $this->assertSame($delivery->getEnabled(), $arrayDelivery['enabled']);
    }
}
