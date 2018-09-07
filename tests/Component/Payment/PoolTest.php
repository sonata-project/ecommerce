<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Tests\Payment;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Payment\PassPayment;
use Sonata\Component\Payment\Pool;
use Symfony\Component\Routing\RouterInterface;

class PoolTest extends TestCase
{
    public function testPool()
    {
        $pool = new Pool();

        $router = $this->createMock(RouterInterface::class);

        $payment = new PassPayment($router);
        $payment->setCode('pass_1');

        $pool->addMethod($payment);

        $payment = new PassPayment($router);
        $payment->setCode('pass_2');

        $pool->addMethod($payment);

        $payment = new PassPayment($router);
        $payment->setCode('pass_2');  // same code

        $pool->addMethod($payment);

        $this->assertCount(2, $pool->getMethods(), 'Pool return 2 elements');
        $this->assertInstanceOf(PassPayment::class, $pool->getMethod('pass_2'), 'Pool return an FreeDelivery Instance');
    }

    public function testAddMethodError()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Payment handler of class Sonata\\Component\\Payment\\PassPayment must return a code on getCode method. Please refer to the documentation (https://sonata-project.org/bundles/ecommerce/master/doc/reference/bundles/payment/index.html)');

        $pool = new Pool();

        $router = $this->createMock(RouterInterface::class);

        $payment = new PassPayment($router);

        $pool->addMethod($payment);
    }
}
