<?php

use Sonata\Component\Transformer\InvoiceTransformer;
use Sonata\Component\Currency\Currency;

use Sonata\Component\Delivery\Pool as DeliveryPool;

/*
 * This file is part of the Sonata package.
*
* (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

class InvoiceTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testTransformFromOrder()
    {
        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');

        $orderElement = $this->getMock('Sonata\Component\Order\OrderElementInterface');
        $orderElement->expects($this->once())->method('getDescription');
        $orderElement->expects($this->once())->method('getDesignation');
        $orderElement->expects($this->once())->method('getPrice')->will($this->returnValue(42));
        $orderElement->expects($this->once())->method('getQuantity')->will($this->returnValue(3));
        $orderElement->expects($this->once())->method('getVatRate');

        $order = $this->getMock('Sonata\Component\Order\OrderInterface');
        $order->expects($this->once())->method('getOrderElements')->will($this->returnValue(array($orderElement)));
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

        $invoice = $this->getMock('Sonata\Component\Invoice\InvoiceInterface');

        $invoiceElement = $this->getMock('Sonata\Component\Invoice\InvoiceElementInterface');

        $invoiceElementManager = $this->getMock('Sonata\Component\Invoice\InvoiceElementManagerInterface');
        $invoiceElementManager->expects($this->once())->method('create')->will($this->returnValue($invoiceElement));

        $deliveryPool = new DeliveryPool();

        $eventDispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $invoiceTransformer = new InvoiceTransformer($invoiceElementManager, $deliveryPool, $eventDispatcher);
        $invoiceTransformer->transformFromOrder($order, $invoice);
    }
}
