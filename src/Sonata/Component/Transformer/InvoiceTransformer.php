<?php
namespace Sonata\Component\Transformer;

use Sonata\Component\Transformer\BaseTransformer;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Invoice\InvoiceInterface;
use Sonata\Component\Invoice\InvoiceElementManagerInterface;
use Sonata\Component\Order\OrderElementInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class InvoiceTransformer extends BaseTransformer
{
    /**
     * @var InvoiceElementManagerInterface
     */
    protected $invoiceElementManager;

    public function __construct(InvoiceElementManagerInterface $invoiceElementManager)
    {
        $this->invoiceElementManager = $invoiceElementManager;
    }

    /**
     * Transforms an order into an invoice
     *
     * @param OrderInterface   $order
     * @param InvoiceInterface $invoice
     */
    public function transformFromOrder(OrderInterface $order, InvoiceInterface $invoice)
    {
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

        foreach ($order->getOrderElements() as $orderElement) {
            $invoiceElement = $this->createInvoiceElementFromOrderElement($orderElement);
            $invoiceElement->setInvoice($invoice);
            $invoice->addInvoiceElement($invoiceElement);
        }

        $invoice->setStatus(InvoiceInterface::STATUS_OPEN);
    }

    /**
     * Creates an InvoiceElement based on an OrderElement
     *
     * @param OrderElementInterface $orderElement
     *
     * @return \Sonata\Component\Invoice\InvoiceElementInterface
     */
    protected function createInvoiceElementFromOrderElement(OrderElementInterface $orderElement)
    {
        $invoice = $this->invoiceElementManager->create();
        $invoice->setOrderElement($orderElement);
        $invoice->setDescription($orderElement->getDescription());
        $invoice->setDesignation($orderElement->getDesignation());
        $invoice->setPrice($orderElement->getPrice());
        $invoice->setQuantity($orderElement->getQuantity());
        $invoice->setTotal($orderElement->getPrice() * $orderElement->getQuantity());
        $invoice->setVat($orderElement->getVat());

        return $invoice;
    }
}