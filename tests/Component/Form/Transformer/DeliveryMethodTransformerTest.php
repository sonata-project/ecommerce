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
use Sonata\Component\Delivery\FreeDelivery;
use Sonata\Component\Delivery\Pool;
use Sonata\Component\Form\Transformer\DeliveryMethodTransformer;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class DeliveryMethodTransformerTest extends TestCase
{
    public function testTransform(): void
    {
        $pool = $this->createMock(Pool::class);
        $transformer = new DeliveryMethodTransformer($pool);

        $delivery = new FreeDelivery(false);
        $delivery->setCode('deliveryCode');

        static::assertSame('deliveryCode', $transformer->transform($delivery));
        static::assertNull($transformer->transform(null));
    }

    public function testReverseTransform(): void
    {
        $delivery = new FreeDelivery(false);
        $delivery->setCode('deliveryCode');

        $pool = $this->createMock(Pool::class);

        $pool->expects(static::once())
            ->method('getMethod')
            ->willReturn($delivery);

        $transformer = new DeliveryMethodTransformer($pool);

        static::assertSame($delivery, $transformer->reverseTransform('deliveryCode'));
    }
}
