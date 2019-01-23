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

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use PHPUnit\Framework\TestCase;
use Sonata\Component\Order\OrderElementInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Order\OrderManagerInterface;
use Sonata\OrderBundle\Controller\Api\OrderController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class OrderControllerTest extends TestCase
{
    public function testGetOrdersAction()
    {
        $orderManager = $this->createMock(OrderManagerInterface::class);
        $orderManager->expects($this->once())->method('getPager')->will($this->returnValue([]));

        $paramFetcher = $this->createMock(ParamFetcher::class);
        $paramFetcher->expects($this->exactly(3))->method('get');
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue([]));
        $paramFetcher->expects($this->once())->method('addParam')->with($this->callback(function ($param) {
            return $param instanceof QueryParam && $param->name = 'orderBy';
        }));

        $this->assertSame([], $this->createOrderController(null, $orderManager)->getOrdersAction($paramFetcher));
    }

    public function testGetOrderAction()
    {
        $order = $this->createMock(OrderInterface::class);
        $this->assertSame($order, $this->createOrderController($order)->getOrderAction(1));
    }

    public function testGetOrderActionNotFoundException()
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Order (42) not found');

        $this->createOrderController()->getOrderAction(42);
    }

    public function testGetOrderOrderelementsAction()
    {
        $order = $this->createMock(OrderInterface::class);
        $orderElements = $this->createMock(OrderElementInterface::class);
        $order->expects($this->once())->method('getOrderElements')->will($this->returnValue([$orderElements]));

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
            $orderManager->expects($this->once())->method('findOneBy')->will($this->returnValue($order));
        }

        return new OrderController($orderManager);
    }
}
