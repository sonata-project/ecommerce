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

namespace Sonata\Component\Tests\Payment\Scellius;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Payment\Scellius\OrderScelliusTransactionGenerator;

class OrderScelliusTransactionGeneratorTest extends TestCase
{
    public function testGenerator(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $order->expects($this->any())->method('getReference')->will($this->returnValue('120112000012'));

        $generator = new OrderScelliusTransactionGenerator();
        $this->assertSame('000012', $generator->generate($order));
    }

    public function testInvalidReferenceLength(): void
    {
        $this->expectException(\RuntimeException::class);

        $order = $this->createMock(OrderInterface::class);
        $order->expects($this->any())->method('getReference')->will($this->returnValue('12'));

        $generator = new OrderScelliusTransactionGenerator();
        $generator->generate($order);
    }
}
