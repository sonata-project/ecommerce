<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\PaymentBundle\Controller;

use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Payment\Debug\DebugPayment;
use Sonata\Component\Payment\InvalidTransactionException;
use Sonata\OrderBundle\Entity\OrderManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouterInterface;

/**
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

        return $this->render('SonataPaymentBundle:Payment:debug.html.twig', [
            'order' => $order,
            'check' => $this->getCurrentRequest()->get('check'),
        ]);
    }

    /**
     * Process the User choice.
     *
     * @return Response
     */
    public function processPaymentAction()
    {
        $order = $this->checkRequest();

        return $this->getDebugPayment()->processCallback($order, $this->getCurrentRequest()->get('action'));
    }

    /**
     * Check the Request and return the current Order.
     *
     * @throws InvalidTransactionException
     * @throws NotFoundHttpException
     * @throws \RuntimeException
     *
     * @return OrderInterface
     */
    protected function checkRequest()
    {
        if ('prod' === $this->getKernel()->getEnvironment()) {
            throw new \RuntimeException('Debug Payment is not authorized in production environment.');
        }

        $request = $this->getCurrentRequest();
        $reference = $request->get('reference');

        $order = $this->getOrderManager()->findOneBy(['reference' => $reference]);

        if (!$order) {
            throw new NotFoundHttpException(sprintf('Order with reference "%s" not found.', $reference));
        }

        if ($request->get('check') !== $this->getDebugPayment()->generateUrlCheck($order)) {
            throw new InvalidTransactionException($reference);
        }

        return $order;
    }

    /**
     * @return DebugPayment
     */
    protected function getDebugPayment()
    {
        return $this->get('sonata.payment.method.debug');
    }

    /**
     * @return OrderManager
     */
    protected function getOrderManager()
    {
        return $this->get('sonata.order.manager');
    }

    /**
     * @return KernelInterface
     */
    protected function getKernel()
    {
        return $this->get('kernel');
    }

    /**
     * @return RouterInterface
     */
    protected function getRouter()
    {
        return $this->get('router');
    }

    /**
     * NEXT_MAJOR: Remove this method (inject Request $request into actions parameters)
     *
     * @return Request
     */
    private function getCurrentRequest()
    {
        return $this->container->get('request_stack')->getCurrentRequest();
    }
}
