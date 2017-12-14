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

namespace Sonata\Component\Tests\Transformer;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Currency\Currency;
use Sonata\Component\Customer\CustomerInterface;
use Sonata\Component\Delivery\Pool as DeliveryPool;
use Sonata\Component\Invoice\InvoiceElementInterface;
use Sonata\Component\Invoice\InvoiceElementManagerInterface;
use Sonata\Component\Invoice\InvoiceInterface;
use Sonata\Component\Order\OrderElementInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Transformer\InvoiceTransformer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class InvoiceTransformerTest extends TestCase
{
    public function testTransformFromOrder(): void
    {
        $customer = $this->createMock(CustomerInterface::class);

        $orderElement = $this->createMock(OrderElementInterface::class);
        $orderElement->expects($this->once())->method('getDescription');
        $orderElement->expects($this->once())->method('getDesignation');
        $orderElement->expects($this->once())->method('getPrice')->will($this->returnValue(42));
        $orderElement->expects($this->once())->method('getQuantity')->will($this->returnValue(3));
        $orderElement->expects($this->once())->method('getVatRate');

        $order = $this->createMock(OrderInterface::class);
        $order->expects($this->once())->method('getOrderElements')->will($this->returnValue([$orderElement]));
        $order->expects($this->once())->method('getCustomer')->will($this->returnValue($customer));

        $order->expects($this->once())->method('getBillingAddress1');
        $order->expects($this->once())->method('getBillingAddress2');
        $order->expects($this->once())->method('getBillingAddress3');
        $order->expects($this->once())->method('getBillingCity');
        $order->expects($this->once())->method('getBillingCountryCode');
        $order->expects($this->once())->method('getBillingPostcode');

        $order->expects($this->once())->method('getBillingEmail');
        $order->expects($this->once())->method('getBillingMobile');
        $order->expects($this->once())->method('getBillingFax');
        $order->expects($this->once())->method('getBillingPhone');
        $order->expects($this->once())->method('getReference');

        $currency = new Currency();
        $currency->setLabel('EUR');
        $order->expects($this->once())->method('getCurrency')->will($this->returnValue($currency));
        $order->expects($this->once())->method('getTotalExcl');
        $order->expects($this->once())->method('getTotalInc');

        $invoice = $this->createMock(InvoiceInterface::class);

        $invoiceElement = $this->createMock(InvoiceElementInterface::class);

        $invoiceElementManager = $this->createMock(InvoiceElementManagerInterface::class);
        $invoiceElementManager->expects($this->once())->method('create')->will($this->returnValue($invoiceElement));

        $deliveryPool = new DeliveryPool();

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $invoiceTransformer = new InvoiceTransformer($invoiceElementManager, $deliveryPool, $eventDispatcher);
        $invoiceTransformer->transformFromOrder($order, $invoice);
    }
}
