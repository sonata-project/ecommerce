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
use Sonata\Component\Delivery\FreeDelivery;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class FreeDeliveryTest extends TestCase
{
    public function testConstruct(): void
    {
        $freeDelivery = new FreeDelivery(false);
        $this->assertFalse($freeDelivery->isAddressRequired());

        $freeDelivery = new FreeDelivery(true);
        $this->assertTrue($freeDelivery->isAddressRequired());
    }

    public function testPriceIsNull(): void
    {
        $freeDelivery = new FreeDelivery(false);
        $this->assertSame(0, $freeDelivery->getVatRate());
        $this->assertSame(0, $freeDelivery->getPrice());
    }

    public function testGetName(): void
    {
        $freeDelivery = new FreeDelivery(false);
        $this->assertSame('Free delivery', $freeDelivery->getName());
    }
}
