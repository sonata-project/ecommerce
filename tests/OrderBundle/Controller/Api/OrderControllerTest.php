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

namespace Sonata\OrderBundle\Tests\Controller\Api;

use FOS\RestBundle\Request\ParamFetcherInterface;
use PHPUnit\Framework\TestCase;
use Sonata\Component\Order\OrderElementInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Order\OrderManagerInterface;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\OrderBundle\Controller\Api\OrderController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class OrderControllerTest extends TestCase
{
    public function testGetOrdersAction(): void
    {
        $pager = $this->createStub(PagerInterface::class);
        $orderManager = $this->createMock(OrderManagerInterface::class);
        $orderManager->expects($this->once())->method('getPager')->willReturn($pager);

        $paramFetcher = $this->createMock(ParamFetcherInterface::class);
        $paramFetcher->expects($this->exactly(3))->method('get')->willReturn(1, 10, null);
        $paramFetcher->expects($this->once())->method('all')->willReturn([]);

        $this->assertSame($pager, $this->createOrderController(null, $orderManager)->getOrdersAction($paramFetcher));
    }

    public function testGetOrderAction(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $this->assertSame($order, $this->createOrderController($order)->getOrderAction(1));
    }

    public function testGetOrderActionNotFoundException(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Order (42) not found');

        $this->createOrderController()->getOrderAction(42);
    }

    public function testGetOrderOrderelementsAction(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $orderElements = $this->createMock(OrderElementInterface::class);
        $order->expects($this->once())->method('getOrderElements')->willReturn([$orderElements]);

        $this->assertSame([$orderElements], $this->createOrderController($order)->getOrderOrderelementsAction(1));
    }

    /**
     * @param $order
     * @param $orderManager
     *
     * @return OrderController
     */
    public function createOrderController($order = null, $orderManager = null)
    {
        if (null === $orderManager) {
            $orderManager = $this->createMock(OrderManagerInterface::class);
        }
        if (null !== $order) {
            $orderManager->expects($this->once())->method('findOneBy')->willReturn($order);
        }

        return new OrderController($orderManager);
    }
}
