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
    public function testBasePayment()
    {
        $payment = new BasePaymentTest_Payment();

        $payment->setCode('test');
        $payment->setOptions([
            'foobar' => 'barfoo',
        ]);
        $this->assertEquals('test', $payment->getCode());
        $this->assertFalse($payment->hasOption('bar'));
        $this->assertEquals('13 134 &*', $payment->encodeString('13 134 &*'));
    }

    public function testGenerateUrlCheck()
    {
        $payment = new BasePaymentTest_Payment();
        $payment->setOptions([
            'shop_secret_key' => 's3cr3t k2y',
        ]);
        $date = new \DateTime();
        $date->setTimestamp(strtotime('1981-11-30'));

        $order = $this->createMock(OrderInterface::class);
        $order->expects($this->once())->method('getReference')->will($this->returnValue('000123'));
        $order->expects($this->exactly(2))->method('getCreatedAt')->will($this->returnValue($date));
        $order->expects($this->once())->method('getId')->will($this->returnValue(2));

        $this->assertEquals('2a084bbe95bb3842813499d4b5b1bfdf82e5a980', $payment->generateUrlCheck($order));
    }
}
