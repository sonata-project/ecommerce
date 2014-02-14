<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\Component\Payment\Scellius;

use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Payment\Scellius\NodeScelliusTransactionGenerator;

class NodeScelliusTransactionGeneratorTest extends \PHPUnit_Framework_TestCase
{

    public function testGenerator()
    {
        $order = $this->getMock('Sonata\Component\Order\OrderInterface');
        $order->expects($this->never())->method('getReference');

        $generator = new NodeScelliusTransactionGenerator;
        $this->assertEquals('', $generator->generate($order));
    }
}
