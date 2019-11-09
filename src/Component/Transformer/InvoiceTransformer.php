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

namespace Sonata\Component\Transformer;

use Sonata\Component\Delivery\Pool as DeliveryPool;
use Sonata\Component\Event\InvoiceTransformEvent;
use Sonata\Component\Event\OrderTransformEvent;
use Sonata\Component\Event\TransformerEvents;
use Sonata\Component\Invoice\InvoiceElementInterface;
use Sonata\Component\Invoice\InvoiceElementManagerInterface;
use Sonata\Component\Invoice\InvoiceInterface;
use Sonata\Component\Order\OrderElementInterface;
use Sonata\Component\Order\OrderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class InvoiceTransformer extends BaseTransformer
{
    /**
     * @var InvoiceElementManagerInterface
     */
    protected $invoiceElementManager;

    /**
     * @var DeliveryPool
     */
    protected $deliveryPool;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param InvoiceElementManagerInterface $invoiceElementManager Invoice element manager
     * @param DeliveryPool                   $deliveryPool          Delivery pool component
     */
    public function __construct(InvoiceElementManagerInterface $invoiceElementManager, DeliveryPool $deliveryPool, EventDispatcherInterface $eventDispatcher)
    {
        $this->invoiceElementManager = $invoiceElementManager;
        $this->deliveryPool = $deliveryPool;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Transforms an order into an invoice.
     */
    public function transformFromOrder(OrderInterface $order, InvoiceInterface $invoice): void
    {
        $event = new OrderTransformEvent($order);
        $this->eventDispatcher->dispatch(TransformerEvents::PRE_ORDER_TO_INVOICE_TRANSFORM, $event);

        $invoice->setName($order->getBillingName());
        $invoice->setAddress1($order->getBillingAddress1());
        $invoice->setAddress2($order->getBillingAddress2());
        $invoice->setAddress3($order->getBillingAddress3());
        $invoice->setCity($order->getBillingCity());
        $invoice->setCountry($order->getBillingCountryCode());
        $invoice->setPostcode($order->getBillingPostcode());

        $invoice->setEmail($order->getBillingEmail());
        $invoice->setFax($order->getBillingFax());
        $invoice->setMobile($order->getBillingMobile());
        $invoice->setPhone($order->getBillingPhone());
        $invoice->setReference($order->getReference());

        $invoice->setCurrency($order->getCurrency());
        $invoice->setCustomer($order->getCustomer());
        $invoice->setTotalExcl($order->getTotalExcl());
        $invoice->setTotalInc($order->getTotalInc());

        $invoice->setPaymentMethod($order->getPaymentMethod());

        $invoice->setLocale($order->getLocale());

        foreach ($order->getOrderElements() as $orderElement) {
            $invoiceElement = $this->createInvoiceElementFromOrderElement($orderElement);
            $invoiceElement->setInvoice($invoice);
            $invoice->addInvoiceElement($invoiceElement);
        }

        if ($order->getDeliveryCost() > 0) {
            $this->addDelivery($invoice, $order);
        }

        $invoice->setStatus(InvoiceInterface::STATUS_OPEN);

        $event = new InvoiceTransformEvent($invoice);
        $this->eventDispatcher->dispatch(TransformerEvents::POST_ORDER_TO_INVOICE_TRANSFORM, $event);
    }

    /**
     * Adds the delivery information from $order to $invoice.
     */
    protected function addDelivery(InvoiceInterface $invoice, OrderInterface $order): void
    {
        /** @var InvoiceElementInterface $invoiceElement */
        $invoiceElement = $this->invoiceElementManager->create();

        $invoiceElement->setQuantity(1);
        $invoiceElement->setPrice($order->getDeliveryCost());
        $invoiceElement->setUnitPriceExcl($order->getDeliveryCost());
        $invoiceElement->setUnitPriceInc($order->getDeliveryCost());
        $invoiceElement->setTotal($order->getDeliveryCost());
        $invoiceElement->setVatRate(0);

        $invoiceElement->setDesignation($this->deliveryPool->getMethod($order->getDeliveryMethod())->getName());
        $invoiceElement->setDescription($this->deliveryPool->getMethod($order->getDeliveryMethod())->getName());

        $invoiceElement->setInvoice($invoice);
        $invoice->addInvoiceElement($invoiceElement);
    }

    /**
     * Creates an InvoiceElement based on an OrderElement.
     *
     * @return \Sonata\Component\Invoice\InvoiceElementInterface
     */
    protected function createInvoiceElementFromOrderElement(OrderElementInterface $orderElement)
    {
        $invoice = $this->invoiceElementManager->create();
        $invoice->setOrderElement($orderElement);
        $invoice->setDescription($orderElement->getDescription());
        $invoice->setDesignation($orderElement->getDesignation());
        $invoice->setPrice($orderElement->getPrice(true));
        $invoice->setUnitPriceExcl($orderElement->getUnitPrice(false));
        $invoice->setUnitPriceInc($orderElement->getUnitPrice(true));
        $invoice->setVatRate($orderElement->getVatRate());
        $invoice->setQuantity($orderElement->getQuantity());
        $invoice->setTotal($orderElement->getTotal(true));

        return $invoice;
    }
}
