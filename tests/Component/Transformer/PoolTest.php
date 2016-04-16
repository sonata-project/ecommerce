<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\tests\Component\Transformer;

use Sonata\Component\Transformer\BasketTransformer;
use Sonata\Component\Transformer\OrderTransformer;
use Sonata\Component\Transformer\Pool as TransformerPool;

class PoolTest extends \PHPUnit_Framework_TestCase
{
    public function testPool()
    {
        $pool = new TransformerPool();

        $transformer = new BasketTransformer(
            $this->getMock('Sonata\Component\Order\OrderManagerInterface'),
            $this->getMock('Sonata\Component\Product\Pool')
        );

        $pool->addTransformer('basket', $transformer);

        $transformer = new OrderTransformer($this->getMock('Sonata\Component\Product\Pool'));
        $pool->addTransformer('order', $transformer);

        $this->assertSame(2, count($pool->getTransformers()), 'Pool return 2 elements');
        $this->assertInstanceOf('Sonata\\Component\\Transformer\\BasketTransformer', $pool->getTransformer('basket'), 'Pool return an FreeDelivery Instance');
    }
}
