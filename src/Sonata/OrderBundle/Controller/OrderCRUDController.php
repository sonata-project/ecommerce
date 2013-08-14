<?php
namespace Sonata\OrderBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Sonata\Component\Transformer\InvoiceTransformer;
use Sonata\InvoiceBundle\Entity\InvoiceManager;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class OrderCRUDController extends CRUDController
{
    public function generateInvoiceAction()
    {
        if (null === ($id = $this->getRequest()->get('id'))) {
            throw new InvalidParameterException("Missing 'id' parameter");
        }

        $order = $this->admin->getObject($id);
        $invoice = $this->getInvoiceManager()->createInvoice();

        $this->getInvoiceTransformer()->transformFromOrder($order, $invoice);

        $this->getInvoiceManager()->updateInvoice($invoice);

        $this->addFlash('sonata_flash_success', 'order_invoice_generate_success');

        return $this->redirectTo($order);
    }

    /**
     * @return InvoiceManager
     */
    protected function getInvoiceManager()
    {
        return $this->get('sonata.invoice.manager');
    }

    /**
     * @return InvoiceTransformer
     */
    protected function getInvoiceTransformer()
    {
        return $this->get('sonata.payment.transformer.invoice');
    }
}