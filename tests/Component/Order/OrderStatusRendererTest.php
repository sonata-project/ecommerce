<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\Tests\Component\Order;

use Sonata\Component\Delivery\BaseServiceDelivery;
use Sonata\Component\Order\OrderStatusRenderer;
use Sonata\Component\Order\OrderInterface;
use Sonata\OrderBundle\Entity\BaseOrder;
use Sonata\PaymentBundle\Entity\BaseTransaction;


/**
 * Class OrderStatusRendererTest
 *
 * @package Sonata\Tests\Component\Order
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class OrderStatusRendererTest extends \PHPUnit_Framework_TestCase
{
    public function testHandles()
    {
        $osRenderer = new OrderStatusRenderer();

        $order = new \DateTime;
        $this->assertFalse($osRenderer->handlesObject($order));

        $order = $this->getMock('Sonata\Component\Order\OrderInterface');
        $this->assertTrue($osRenderer->handlesObject($order));

        $order = $this->getMock('Sonata\Component\Order\OrderElementInterface');
        $this->assertTrue($osRenderer->handlesObject($order));

        foreach (array('delivery', 'payment') as $correctStatusType) {
            $this->assertTrue($osRenderer->handlesObject($order, $correctStatusType));
        }

        $this->assertFalse($osRenderer->handlesObject($order, 'toubidou'));
    }

    public function testGetClass()
    {
        $osRenderer = new OrderStatusRenderer();

        $order = $this->getMock('Sonata\Component\Order\OrderInterface');
        $order->expects($this->once())->method('getStatus')->will($this->returnValue(array_rand(BaseOrder::getStatusList())));
        $order->expects($this->once())->method('getDeliveryStatus')->will($this->returnValue(array_rand(BaseServiceDelivery::getStatusList())));
        $order->expects($this->once())->method('getPaymentStatus')->will($this->returnValue(array_rand(BaseTransaction::getStatusList())));

        $this->assertContains($osRenderer->getStatusClass($order, "", "error"), array('success', 'info', 'error'));
        $this->assertContains($osRenderer->getStatusClass($order, "payment", "error"), array('success', 'info', 'error'));
        $this->assertContains($osRenderer->getStatusClass($order, "delivery", "error"), array('success', 'info', 'error'));
    }

    public function testGetInvalidClass()
    {
        $osRenderer = new OrderStatusRenderer();

        $order = $this->getMock('Sonata\Component\Order\OrderInterface');
        $order->expects($this->once())->method('getStatus')->will($this->returnValue(8));

        $this->assertEquals("default_value", $osRenderer->getStatusClass($order, "toubidou", "default_value"));
    }
}
