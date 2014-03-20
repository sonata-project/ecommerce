<?php
namespace Sonata\OrderBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Sonata\Component\Transformer\InvoiceTransformer;
use Sonata\Component\Invoice\InvoiceManagerInterface;

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

        if (null === $this->getRequest()->get('confirm')) {
            return $this->render('SonataOrderBundle:OrderAdmin:invoice_generate_confirm.html.twig', array('id' => $id));
        }

        $order = $this->admin->getObject($id);

        $invoice = $this->getInvoiceManager()->findOneBy(array('reference' => $order->getReference()));

        if (null === $invoice) {
            $invoice = $this->getInvoiceManager()->create();

            $this->getInvoiceTransformer()->transformFromOrder($order, $invoice);
            $this->getInvoiceManager()->save($invoice);

            $this->addFlash('sonata_flash_success', $this->get('translator')->trans('oRDER_TO_INVOICE_generate_success', array(), 'SonataOrderBundle'));
        }

        return $this->redirect($this->generateUrl('admin_sonata_invoice_invoice_edit', array('id' => $invoice->getId())));
    }

    /**
     * @return InvoiceManagerInterface
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
