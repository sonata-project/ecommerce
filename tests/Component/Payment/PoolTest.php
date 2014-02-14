<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\Component\Payment;

use Sonata\Component\Payment\PassPayment;
use Sonata\Component\Payment\Pool;

class PoolTest extends \PHPUnit_Framework_TestCase
{
    public function testPool()
    {
        $pool = new Pool;

        $router = $this->getMock('Symfony\Component\Routing\RouterInterface');

        $delivery = new PassPayment($router);
        $delivery->setCode('pass_1');

        $pool->addMethod($delivery);

        $delivery = new PassPayment($router);
        $delivery->setCode('pass_2');

        $pool->addMethod($delivery);

        $delivery = new PassPayment($router);
        $delivery->setCode('pass_2');  // same code

        $pool->addMethod($delivery);

        $this->assertEquals(2, count($pool->getMethods()), 'Pool return 2 elements');
        $this->assertInstanceOf('Sonata\\Component\\Payment\\PassPayment', $pool->getMethod('pass_2'), 'Pool return an FreeDelivery Instance');
    }
}
