<?php

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
use Sonata\Component\Transformer\BasketTransformer;
use Sonata\Component\Transformer\OrderTransformer;
use Sonata\Component\Transformer\Pool as TransformerPool;

class PoolTest extends TestCase
{
    public function testPool()
    {
        $pool = new TransformerPool();

        $transformer = new BasketTransformer(
            $this->createMock('Sonata\Component\Order\OrderManagerInterface'),
            $this->createMock('Sonata\Component\Product\Pool'),
            $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')
        );

        $pool->addTransformer('basket', $transformer);

        $transformer = new OrderTransformer(
            $this->createMock('Sonata\Component\Product\Pool'),
            $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface')
        );
        $pool->addTransformer('order', $transformer);

        $this->assertEquals(2, count($pool->getTransformers()), 'Pool return 2 elements');
        $this->assertInstanceOf('Sonata\\Component\\Transformer\\BasketTransformer', $pool->getTransformer('basket'), 'Pool return an FreeDelivery Instance');
    }
}
