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
use Sonata\Component\Order\OrderInterface;

class BasePaymentTest extends TestCase
{
    public function testBasePayment(): void
    {
        $payment = new BasePaymentTest_Payment();

        $payment->setCode('test');
        $payment->setOptions([
            'foobar' => 'barfoo',
        ]);
        $this->assertSame('test', $payment->getCode());
        $this->assertFalse($payment->hasOption('bar'));
        $this->assertSame('13 134 &*', $payment->encodeString('13 134 &*'));
    }

    public function testGenerateUrlCheck(): void
    {
        $payment = new BasePaymentTest_Payment();
        $payment->setOptions([
            'shop_secret_key' => 's3cr3t k2y',
        ]);
        $date = new \DateTime();
        $date->setTimestamp(strtotime('1981-11-30'));

        $order = $this->createMock(OrderInterface::class);
        $order->expects($this->once())->method('getReference')->willReturn('000123');
        $order->expects($this->exactly(2))->method('getCreatedAt')->willReturn($date);
        $order->expects($this->once())->method('getId')->willReturn(2);

        $this->assertSame('2a084bbe95bb3842813499d4b5b1bfdf82e5a980', $payment->generateUrlCheck($order));
    }
}
