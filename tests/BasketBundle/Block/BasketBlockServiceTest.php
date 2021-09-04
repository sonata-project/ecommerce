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

namespace Sonata\BasketBundle\Tests\Block;

use PHPUnit\Framework\TestCase;
use Sonata\BasketBundle\Block\BasketBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Xavier Coureau <xcoureau@ekino.com>
 */
class BasketBlockServiceTest extends TestCase
{
    public function testGetName(): void
    {
        $environment = $this->createMock(\Twig\Environment::class);
        $engineInterfaceMock = $this->createMock(EngineInterface::class);
        $block = new BasketBlockService($environment, $engineInterfaceMock);

        static::assertSame('Basket items', $block->getName());
    }

    public function testExecute(): void
    {
        $environment = $this->createMock(\Twig\Environment::class);

        $engineInterfaceMock = $this->createMock(EngineInterface::class);
        $engineInterfaceMock->expects(static::any())->method('renderResponse')->willReturn(new Response('test'));

        $context = $this->createMock(BlockContextInterface::class);
        $block = new BasketBlockService($environment, $engineInterfaceMock);

        static::assertInstanceOf(Response::class, $block->execute($context));
    }
}
