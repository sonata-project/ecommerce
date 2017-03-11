<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Tests\Payment\Scellius;

use Sonata\Component\Payment\Scellius\OrderScelliusTransactionGenerator;
use Sonata\Tests\Helpers\PHPUnit_Framework_TestCase;

class OrderScelliusTransactionGeneratorTest extends PHPUnit_Framework_TestCase
{
    public function testGenerator()
    {
        $order = $this->createMock('Sonata\Component\Order\OrderInterface');
        $order->expects($this->any())->method('getReference')->will($this->returnValue('120112000012'));

        $generator = new OrderScelliusTransactionGenerator();
        $this->assertEquals('000012', $generator->generate($order));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testInvalidReferenceLength()
    {
        $order = $this->createMock('Sonata\Component\Order\OrderInterface');
        $order->expects($this->any())->method('getReference')->will($this->returnValue('12'));

        $generator = new OrderScelliusTransactionGenerator();
        $generator->generate($order);
    }
}
