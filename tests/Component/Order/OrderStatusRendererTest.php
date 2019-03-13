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

namespace Sonata\Component\Tests\Order;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Delivery\BaseServiceDelivery;
use Sonata\Component\Order\OrderElementInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Order\OrderStatusRenderer;
use Sonata\OrderBundle\Entity\BaseOrder;
use Sonata\PaymentBundle\Entity\BaseTransaction;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class OrderStatusRendererTest extends TestCase
{
    public function testHandles(): void
    {
        $osRenderer = new OrderStatusRenderer();

        $order = new \DateTime();
        $this->assertFalse($osRenderer->handlesObject($order));

        $order = $this->createMock(OrderInterface::class);
        $this->assertTrue($osRenderer->handlesObject($order));

        $order = $this->createMock(OrderElementInterface::class);
        $this->assertTrue($osRenderer->handlesObject($order));

        foreach (['delivery', 'payment'] as $correctStatusType) {
            $this->assertTrue($osRenderer->handlesObject($order, $correctStatusType));
        }

        $this->assertFalse($osRenderer->handlesObject($order, 'toubidou'));
    }

    public function testGetClass(): void
    {
        $osRenderer = new OrderStatusRenderer();

        $order = $this->createMock(OrderInterface::class);
        $order->expects($this->once())->method('getStatus')->will($this->returnValue(array_rand(BaseOrder::getStatusList())));
        $order->expects($this->once())->method('getDeliveryStatus')->will($this->returnValue(array_rand(BaseServiceDelivery::getStatusList())));
        $order->expects($this->once())->method('getPaymentStatus')->will($this->returnValue(array_rand(BaseTransaction::getStatusList())));

        $this->assertContains($osRenderer->getStatusClass($order, '', 'error'), ['success', 'info', 'error']);
        $this->assertContains($osRenderer->getStatusClass($order, 'payment', 'error'), ['success', 'info', 'error']);
        $this->assertContains($osRenderer->getStatusClass($order, 'delivery', 'error'), ['success', 'info', 'error']);
    }

    public function testGetInvalidClass(): void
    {
        $osRenderer = new OrderStatusRenderer();

        $order = $this->createMock(OrderInterface::class);
        $order->expects($this->once())->method('getStatus')->will($this->returnValue(8));

        $this->assertSame('default_value', $osRenderer->getStatusClass($order, 'toubidou', 'default_value'));
    }
}
