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

namespace Sonata\Component\Tests\Payment;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Payment\PaymentNotFoundException;
use Sonata\Component\Payment\Pool as PaymentPool;
use Sonata\Component\Payment\Selector;
use Sonata\Component\Product\Pool as ProductPool;

/**
 * @author Xavier Coureau <xcoureau@ekino.com>
 */
class SelectorTest extends TestCase
{
    public function testGetPaymentPool(): void
    {
        $paymentPoolMethods = ['first method', 'second method'];

        $paymentPool = $this->getMockBuilder(PaymentPool::class)->getMock();
        $paymentPool->expects($this->any())
            ->method('getMethods')
            ->will($this->returnValue($paymentPoolMethods));

        $productPool = $this->getMockBuilder(ProductPool::class)->getMock();

        $selector = new Selector($paymentPool, $productPool);
        $this->assertFalse($selector->getAvailableMethods());
        $this->assertSame($paymentPoolMethods, $selector->getAvailableMethods(null, new Address()));
    }

    public function testGetPaymentException(): void
    {
        $this->expectException(PaymentNotFoundException::class);
        $this->expectExceptionMessage('Payment method with code \'not_existing\' was not found');

        $paymentPoolMethods = ['first method', 'second method'];

        $paymentPool = $this->getMockBuilder(PaymentPool::class)->getMock();
        $paymentPool->expects($this->any())
            ->method('getMethods')
            ->will($this->returnValue($paymentPoolMethods));

        $productPool = $this->getMockBuilder(ProductPool::class)->getMock();

        $selector = new Selector($paymentPool, $productPool);

        $selector->getPayment('not_existing');
    }
}
