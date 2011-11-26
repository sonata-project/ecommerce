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

use Sonata\Component\Delivery\FreeDelivery;
use Sonata\Component\Delivery\Pool;

class BasketElementTest extends \PHPUnit_Framework_TestCase
{
    public function testPool()
    {
        $pool = new Pool;

        $delivery = new FreeDelivery;
        $delivery->setCode('free_1');

        $pool->addMethod($delivery);

        $delivery = new FreeDelivery;
        $delivery->setCode('free_2');

        $pool->addMethod($delivery);

        $delivery = new FreeDelivery;
        $delivery->setCode('free_2');  // same code

        $pool->addMethod($delivery);

        $this->assertEquals(2, count($pool->getMethods()), 'Pool return 2 elements');
        $this->assertInstanceOf('Sonata\\Component\\Delivery\\FreeDelivery', $pool->getMethod('free_2'), 'Pool return an FreeDelivery Instance');
    }


    /**
     * useless test ....
     *
     * @return void
     */
    public function testFreeDelivery()
    {

        $delivery = new FreeDelivery;
        $delivery->setCode('free_1');

        $this->assertEquals(0, $delivery->getPrice(), 'FreeDelivery.price = 0');
        $this->assertEquals(0, $delivery->getVat(), 'FreeDelivery.vat = 0');
    }
}