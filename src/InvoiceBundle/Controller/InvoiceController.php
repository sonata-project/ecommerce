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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Sonata\Component\Invoice\InvoiceManagerInterface;
use Sonata\Component\Transformer\InvoiceTransformer;
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
     * @throws AccessDeniedException
     */
    public function viewAction($reference)
    {
        $order = $this->getOrderManager()->findOneBy(array('reference' => $reference));

        if (null === $order) {
            throw new AccessDeniedException();
        }

        $this->checkAccess($order->getCustomer());

        $invoice = $this->getInvoiceManager()->findOneBy(array('reference' => $reference));

        if (null === $invoice) {
            $invoice = $this->getInvoiceManager()->create();

            $this->getInvoiceTransformer()->transformFromOrder($order, $invoice);
            $this->getInvoiceManager()->save($invoice);
        }

        $this->get('sonata.seo.page')->setTitle($this->get('translator')->trans('invoice_view_title', array(), "SonataInvoiceBundle"));

        return $this->render('SonataInvoiceBundle:Invoice:view.html.twig', array(
            'invoice' => $invoice,
            'order'   => $order,
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
     * @throws AccessDeniedException
     */
    protected function checkAccess(CustomerInterface $customer)
    {
        if (!($user = $this->getUser())
            || !$customer->getUser()
            || $customer->getUser()->getId() !== $user->getId()) {
            throw new AccessDeniedException();
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
