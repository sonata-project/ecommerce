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

namespace Sonata\Component\Tests\Form\Transformer;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Form\Transformer\PaymentMethodTransformer;
use Sonata\Component\Payment\PassPayment;
use Sonata\Component\Payment\Pool;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class PaymentMethodTransformerTest extends TestCase
{
    public function testTransform(): void
    {
        $pool = $this->createMock(Pool::class);
        $transformer = new PaymentMethodTransformer($pool);

        $payment = new PassPayment($this->createMock(RouterInterface::class));
        $payment->setCode('paymentCode');

        $this->assertSame('paymentCode', $transformer->transform($payment));
        $this->assertNull($transformer->transform(null));
    }

    public function testReverseTransform(): void
    {
        $payment = new PassPayment($this->createMock(RouterInterface::class));
        $payment->setCode('paymentCode');

        $pool = $this->createMock(Pool::class);

        $pool->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue($payment));

        $transformer = new PaymentMethodTransformer($pool);

        $this->assertSame($payment, $transformer->reverseTransform('paymentCode'));
    }
}
