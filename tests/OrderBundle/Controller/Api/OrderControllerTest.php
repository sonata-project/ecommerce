<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Test\OrderBundle\Controller\Api;

use Sonata\OrderBundle\Controller\Api\OrderController;

/**
 * Class OrderControllerTest.
 *
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class OrderControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetOrdersAction()
    {
        $order        = $this->getMock('Sonata\Component\Order\OrderInterface');
        $orderManager = $this->getMock('Sonata\Component\Order\OrderManagerInterface');
        $orderManager->expects($this->once())->method('findBy')->will($this->returnValue(array($order)));

        $paramFetcher = $this->getMock('FOS\RestBundle\Request\ParamFetcherInterface');
        $paramFetcher->expects($this->exactly(3))->method('get');
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue(array()));

        $this->assertSame(array($order), $this->createOrderController(null, $orderManager)->getOrdersAction($paramFetcher));
    }

    public function testGetOrderAction()
    {
        $order = $this->getMock('Sonata\Component\Order\OrderInterface');
        $this->assertSame($order, $this->createOrderController($order)->getOrderAction(1));
    }

    /**
     * @expectedException        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Order (42) not found
     */
    public function testGetOrderActionNotFoundException()
    {
        $this->createOrderController()->getOrderAction(42);
    }

    public function testGetOrderOrderelementsAction()
    {
        $order         = $this->getMock('Sonata\Component\Order\OrderInterface');
        $orderElements = $this->getMock('Sonata\Component\Order\OrderElementsInterface');
        $order->expects($this->once())->method('getOrderElements')->will($this->returnValue(array($orderElements)));

        $this->assertSame(array($orderElements), $this->createOrderController($order)->getOrderOrderelementsAction(1));
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
            $orderManager = $this->getMock('Sonata\Component\Order\OrderManagerInterface');
        }
        if (null !== $order) {
            $orderManager->expects($this->once())->method('findOneBy')->will($this->returnValue($order));
        }

        return new OrderController($orderManager);
    }
}
