<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\PaymentBundle\Controller;

use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Payment\InvalidTransactionException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class DebugPaymentController
 *
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
class DebugPaymentController extends Controller
{
    /**
     * User choice action.
     *
     * @return Response
     */
    public function paymentAction()
    {
        $order = $this->checkRequest();

        return $this->render('SonataPaymentBundle:Payment:debug.html.twig', array(
            'order' => $order,
            'check' => $this->getRequest()->get('check'),
        ));
    }

    /**
     * Process the User choice.
     *
     * @return Reponse
     */
    public function processPaymentAction()
    {
        $order = $this->checkRequest();

        return $this->getDebugPayment()->processCallback($order, $this->getRequest()->get('action'));
    }

    /**
     * Check the Request and return the current Order.
     *
     * @return OrderInterface
     *
     * @throws InvalidTransactionException
     * @throws NotFoundHttpException
     * @throws \RuntimeException
     */
    protected function checkRequest()
    {
        if ('prod' === $this->getKernel()->getEnvironment()) {
            throw new \RuntimeException('Debug Payment is not authorized in production environment.');
        }

        $reference = $this->getRequest()->get('reference');

        $order = $this->getOrderManager()->findOneBy(array('reference' => $reference));

        if (!$order) {
            throw new NotFoundHttpException(sprintf('Order with reference "%s" not found.', $reference));
        }

        if ($this->getRequest()->get('check') !== $this->getDebugPayment()->generateUrlCheck($order)) {
            throw new InvalidTransactionException($reference);
        }

        return $order;
    }

    /**
     * @return \Sonata\Component\Payment\Debug\DebugPayment
     */
    protected function getDebugPayment()
    {
        return $this->get('sonata.payment.method.debug');
    }

    /**
     * @return \Sonata\OrderBundle\Entity\OrderManager
     */
    protected function getOrderManager()
    {
        return $this->get('sonata.order.manager');
    }

    /**
     * @return \Symfony\Component\HttpKernel\Kernel
     */
    protected function getKernel()
    {
        return $this->get('kernel');
    }

    /**
     * @return \Symfony\Component\Routing\RouterInterface
     */
    protected function getRouter()
    {
        return $this->get('router');
    }
}
