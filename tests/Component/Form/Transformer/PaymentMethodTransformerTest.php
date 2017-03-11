<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Tests\Form\Transformer;

use Sonata\Component\Form\Transformer\PaymentMethodTransformer;
use Sonata\Component\Payment\PassPayment;
use Sonata\Tests\Helpers\PHPUnit_Framework_TestCase;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class PaymentMethodTransformerTest extends PHPUnit_Framework_TestCase
{
    public function testTransform()
    {
        $pool = $this->createMock('Sonata\Component\Payment\Pool');
        $transformer = new PaymentMethodTransformer($pool);

        $payment = new PassPayment($this->createMock('Symfony\Component\Routing\RouterInterface'));
        $payment->setCode('paymentCode');

        $this->assertEquals('paymentCode', $transformer->transform($payment));
        $this->assertNull($transformer->transform(null));
    }

    public function testReverseTransform()
    {
        $payment = new PassPayment($this->createMock('Symfony\Component\Routing\RouterInterface'));
        $payment->setCode('paymentCode');

        $pool = $this->createMock('Sonata\Component\Payment\Pool');

        $pool->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue($payment));

        $transformer = new PaymentMethodTransformer($pool);

        $this->assertEquals($payment, $transformer->reverseTransform('paymentCode'));
    }
}
