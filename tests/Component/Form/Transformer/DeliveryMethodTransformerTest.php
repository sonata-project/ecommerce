<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\tests\Component\Form\Transformer;

use Sonata\Component\Delivery\FreeDelivery;
use Sonata\Component\Form\Transformer\DeliveryMethodTransformer;

/**
 * Class DeliveryMethodTransformerTest.
 *
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class DeliveryMethodTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testTransform()
    {
        $pool = $this->getMockBuilder('Sonata\Component\Delivery\Pool')->disableOriginalConstructor()->getMock();
        $transformer = new DeliveryMethodTransformer($pool);

        $delivery = new FreeDelivery(false);
        $delivery->setCode('deliveryCode');

        $this->assertSame('deliveryCode', $transformer->transform($delivery));
        $this->assertNull($transformer->transform(null));
    }

    public function testReverseTransform()
    {
        $delivery = new FreeDelivery(false);
        $delivery->setCode('deliveryCode');

        $pool = $this->getMockBuilder('Sonata\Component\Delivery\Pool')->disableOriginalConstructor()->getMock();

        $pool->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue($delivery));

        $transformer = new DeliveryMethodTransformer($pool);

        $this->assertSame($delivery, $transformer->reverseTransform('deliveryCode'));
    }
}
