<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\Component\Form\Transformer;

use Sonata\Component\Form\Transformer\PaymentMethodTransformer;
use Sonata\Component\Payment\PassPayment;

/**
 * Class PaymentMethodTransformerTest
 *
 * @package Sonata\Tests\Component\Form\Transformer
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class PaymentMethodTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testTransform()
    {
        $pool = $this->getMockBuilder('Sonata\Component\Payment\Pool')->disableOriginalConstructor()->getMock();
        $transformer = new PaymentMethodTransformer($pool);

        $payment = new PassPayment($this->getMock('Symfony\Component\Routing\RouterInterface'));
        $payment->setCode("paymentCode");

        $this->assertEquals("paymentCode", $transformer->transform($payment));
        $this->assertNull($transformer->transform(null));
    }

    public function testReverseTransform()
    {
        $payment = new PassPayment($this->getMock('Symfony\Component\Routing\RouterInterface'));
        $payment->setCode("paymentCode");

        $pool = $this->getMockBuilder('Sonata\Component\Payment\Pool')->disableOriginalConstructor()->getMock();

        $pool->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue($payment));

        $transformer = new PaymentMethodTransformer($pool);

        $this->assertEquals($payment, $transformer->reverseTransform("paymentCode"));
    }
}
