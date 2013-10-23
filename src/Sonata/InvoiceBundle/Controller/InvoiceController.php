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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use Sonata\Component\Invoice\InvoiceManagerInterface;
use Sonata\Component\Transformer\InvoiceTransformer;
use Sonata\Component\Invoice\InvoiceInterface;
use Sonata\Component\Customer\CustomerInterface;

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
     *
     * @return Response
     * 
     * @throws AccessDeniedHttpException
     */
    public function viewAction($reference)
    {
        $invoice = $this->getInvoiceManager()->findInvoiceBy(array('reference' => $reference));

        if (null === $invoice) {
            $order = $this->getOrderManager()->findOneBy(array('reference' => $reference));

            if (null === $order) {
                throw new AccessDeniedHttpException();
            }

            $this->checkAccess($order->getCustomer());

            $invoice = $this->getInvoiceManager()->createInvoice();

            $this->getInvoiceTransformer()->transformFromOrder($order, $invoice);
            $this->getInvoiceManager()->updateInvoice($invoice);
        } else {
            $this->checkAccess($invoice->getCustomer());
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
     * Checks that the current logged in user has access to given invoice
     *
     * @param CustomerInterface $customer The linked customer
     *
     * @throws AccessDeniedHttpException
     */
    protected function checkAccess(CustomerInterface $customer)
    {
        if (!($user = $this->getUser())
            || !$customer->getUser()
            || $customer->getUser()->getId() !== $user->getId()) {
            throw new AccessDeniedHttpException();
        }
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
