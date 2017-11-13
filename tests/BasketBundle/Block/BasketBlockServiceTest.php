<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\BasketBundle\Tests\Block;

use PHPUnit\Framework\TestCase;
use Sonata\BasketBundle\Block\BasketBlockService;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Xavier Coureau <xcoureau@ekino.com>
 */
class BasketBlockServiceTest extends TestCase
{
    public function testGetName()
    {
        $engineInterfaceMock = $this->createMock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
        $block = new BasketBlockService('test', $engineInterfaceMock);

        $this->assertEquals('Basket items', $block->getName());
    }

    public function testExecute()
    {
        $engineInterfaceMock = $this->createMock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
        $engineInterfaceMock->expects($this->once())->method('renderResponse')->will($this->returnValue(new Response()));
        $context = $this->createMock('Sonata\BlockBundle\Block\BlockContextInterface');
        $block = new BasketBlockService('test', $engineInterfaceMock);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $block->execute($context));
    }
}
