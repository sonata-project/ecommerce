<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\InvoiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sonata\Component\Invoice\InvoiceManagerInterface;
use Sonata\Component\Transformer\InvoiceTransformer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InvoiceController extends Controller
{
    /**
     * @throws \RuntimeException
     */
    public function indexAction()
    {
        throw new \RuntimeException('not implemented');
    }

    /**
     * @param string $reference
     */
    public function viewAction($reference)
    {
        $invoice = $this->getInvoiceManager()->findInvoiceBy(array('reference' => $reference));

        if (null === $invoice) {
            $invoice = $this->getInvoiceManager()->createInvoice();
            $order = $this->getOrderManager()->findOneBy(array('reference' => $reference));

            if (null === $order) {
                throw new NotFoundHttpException("Order with reference ".$reference." could not be found.");
            }

            $this->getInvoiceTransformer()->transformFromOrder($order, $invoice);
        }

        return $this->render('SonataInvoiceBundle:Invoice:view.html.twig', array(
            'invoice' => $invoice,
        ));
    }

    /**
     * @param  string            $reference
     * @throws \RuntimeException
     */
    public function downloadAction($reference)
    {
        throw new \RuntimeException('not implemented');
    }

    /**
     * @return InvoiceManagerInterface
     */
    protected function getInvoiceManager()
    {
        return $this->get('sonata.invoice.manager');
    }

    /**
     * @return OrderManagerInterface
     */
    protected function getOrderManager()
    {
        return $this->get('sonata.order.manager');
    }

    /**
     * @return InvoiceTransformer
     */
    protected function getInvoiceTransformer()
    {
        return $this->get('sonata.payment.transformer.invoice');
    }
}
