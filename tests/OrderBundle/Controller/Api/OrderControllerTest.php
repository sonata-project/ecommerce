<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\Test\OrderBundle\Controller\Api;

use Sonata\OrderBundle\Controller\Api\OrderController;


/**
 * Class OrderControllerTest
 *
 * @package Sonata\Test\OrderBundle\Controller\Api
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class OrderControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetOrdersAction()
    {
        $orderManager = $this->getMock('Sonata\Component\Order\OrderManagerInterface');
        $orderManager->expects($this->once())->method('getPager')->will($this->returnValue(array()));

        $paramFetcher = $this->getMock('FOS\RestBundle\Request\ParamFetcherInterface');
        $paramFetcher->expects($this->exactly(3))->method('get');
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue(array()));

        $this->assertEquals(array(), $this->createOrderController(null, $orderManager)->getOrdersAction($paramFetcher));
    }

    public function testGetOrderAction()
    {
        $order = $this->getMock('Sonata\Component\Order\OrderInterface');
        $this->assertEquals($order, $this->createOrderController($order)->getOrderAction(1));
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

        $this->assertEquals(array($orderElements), $this->createOrderController($order)->getOrderOrderelementsAction(1));
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
