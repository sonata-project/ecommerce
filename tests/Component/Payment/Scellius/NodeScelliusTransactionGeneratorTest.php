<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Tests\Payment\Scellius;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Payment\Scellius\NodeScelliusTransactionGenerator;

class NodeScelliusTransactionGeneratorTest extends TestCase
{
    public function testGenerator()
    {
        $order = $this->createMock('Sonata\Component\Order\OrderInterface');
        $order->expects($this->never())->method('getReference');

        $generator = new NodeScelliusTransactionGenerator();
        $this->assertEquals('', $generator->generate($order));
    }
}
