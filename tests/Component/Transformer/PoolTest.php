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

namespace Sonata\Component\Tests\Transformer;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Order\OrderManagerInterface;
use Sonata\Component\Product\Pool;
use Sonata\Component\Transformer\BasketTransformer;
use Sonata\Component\Transformer\OrderTransformer;
use Sonata\Component\Transformer\Pool as TransformerPool;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PoolTest extends TestCase
{
    public function testPool(): void
    {
        $pool = new TransformerPool();

        $transformer = new BasketTransformer(
            $this->createMock(OrderManagerInterface::class),
            $this->createMock(Pool::class),
            $this->createMock(EventDispatcherInterface::class)
        );

        $pool->addTransformer('basket', $transformer);

        $transformer = new OrderTransformer(
            $this->createMock(Pool::class),
            $this->createMock(EventDispatcherInterface::class)
        );
        $pool->addTransformer('order', $transformer);

        $this->assertEquals(2, count($pool->getTransformers()), 'Pool return 2 elements');
        $this->assertInstanceOf(BasketTransformer::class, $pool->getTransformer('basket'), 'Pool return an FreeDelivery Instance');
    }
}
