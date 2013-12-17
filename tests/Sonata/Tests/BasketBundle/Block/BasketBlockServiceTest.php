<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\BasketBundle\Block;

use Sonata\BasketBundle\Block\BasketBlockService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BasketBlockServiceTest
 *
 * @package Sonata\Tests\BasketBundle
 *
 * @author Xavier Coureau <xcoureau@ekino.com>
 */
class BasketBlockServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $engineInterfaceMock = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface')->disableOriginalConstructor()->getMock();
        $block = new BasketBlockService('test', $engineInterfaceMock);

        $this->assertEquals('Basket items', $block->getName());
    }

    public function testExecute()
    {
        $engineInterfaceMock = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface')->disableOriginalConstructor()->getMock();
        $engineInterfaceMock->expects($this->once())->method('renderResponse')->will($this->returnValue(new Response()));
        $context = $this->getMock('Sonata\BlockBundle\Block\BlockContextInterface');
        $block = new BasketBlockService('test', $engineInterfaceMock);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $block->execute($context));
    }
}
