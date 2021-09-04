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
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class InvoiceTransformerTest extends TestCase
{
    public function testTransformFromOrder(): void
    {
        $customer = $this->createMock(CustomerInterface::class);

        $orderElement = $this->createMock(OrderElementInterface::class);
        $orderElement->expects(static::once())->method('getDescription');
        $orderElement->expects(static::once())->method('getDesignation');
        $orderElement->expects(static::once())->method('getPrice')->willReturn(42);
        $orderElement->expects(static::once())->method('getQuantity')->willReturn(3);
        $orderElement->expects(static::once())->method('getVatRate');

        $order = $this->createMock(OrderInterface::class);
        $order->expects(static::once())->method('getOrderElements')->willReturn([$orderElement]);
        $order->expects(static::once())->method('getCustomer')->willReturn($customer);

        $order->expects(static::once())->method('getBillingAddress1');
        $order->expects(static::once())->method('getBillingAddress2');
        $order->expects(static::once())->method('getBillingAddress3');
        $order->expects(static::once())->method('getBillingCity');
        $order->expects(static::once())->method('getBillingCountryCode');
        $order->expects(static::once())->method('getBillingPostcode');

        $order->expects(static::once())->method('getBillingEmail');
        $order->expects(static::once())->method('getBillingMobile');
        $order->expects(static::once())->method('getBillingFax');
        $order->expects(static::once())->method('getBillingPhone');
        $order->expects(static::once())->method('getReference');

        $currency = new Currency();
        $currency->setLabel('EUR');
        $order->expects(static::once())->method('getCurrency')->willReturn($currency);
        $order->expects(static::once())->method('getTotalExcl');
        $order->expects(static::once())->method('getTotalInc');

        $invoice = $this->createMock(InvoiceInterface::class);

        $invoiceElement = $this->createMock(InvoiceElementInterface::class);

        $invoiceElementManager = $this->createMock(InvoiceElementManagerInterface::class);
        $invoiceElementManager->expects(static::once())->method('create')->willReturn($invoiceElement);

        $deliveryPool = new DeliveryPool();

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $invoiceTransformer = new InvoiceTransformer($invoiceElementManager, $deliveryPool, $eventDispatcher);
        $invoiceTransformer->transformFromOrder($order, $invoice);
    }
}
