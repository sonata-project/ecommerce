<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\tests\Component\Delivery;

use Sonata\Component\Delivery\FreeDelivery;

/**
 * Class FreeDeliveryTest.
 *
 *
 * @author  Hugo Briand <briand@ekino.com>
 */
class FreeDeliveryTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $freeDelivery = new FreeDelivery(false);
        $this->assertFalse($freeDelivery->isAddressRequired());

        $freeDelivery = new FreeDelivery(true);
        $this->assertTrue($freeDelivery->isAddressRequired());
    }

    public function testPriceIsNull()
    {
        $freeDelivery = new FreeDelivery(false);
        $this->assertSame(0, $freeDelivery->getVatRate());
        $this->assertSame(0, $freeDelivery->getPrice());
    }

    public function testGetName()
    {
        $freeDelivery = new FreeDelivery(false);
        $this->assertSame('Free delivery', $freeDelivery->getName());
    }
}
