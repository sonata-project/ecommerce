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

namespace Sonata\PaymentBundle\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Sonata\Component\Basket\Basket;
use Sonata\Component\Basket\BasketFactoryInterface;
use Sonata\Component\Payment\InvalidTransactionException;
use Sonata\Component\Payment\PaymentHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class PaymentController extends Controller
{
    /**
     * This action is called by the user after the sendbank
     * In most case the order is already cancelled by a previous callback.
     *
     * @throws UnauthorizedHttpException
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function errorAction()
    {
        try {
            $order = $this->getPaymentHandler()->handleError($this->getCurrentRequest(), $this->getBasket());
        } catch (EntityNotFoundException $ex) {
            throw new NotFoundHttpException($ex->getMessage());
        } catch (InvalidTransactionException $ex) {
            throw new UnauthorizedHttpException($ex->getMessage());
        }

        return $this->render('SonataPaymentBundle:Payment:error.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     * @throws UnauthorizedHttpException
     *
     * @return Response
     */
    public function confirmationAction()
    {
        try {
            $order = $this->getPaymentHandler()->handleConfirmation($this->getCurrentRequest());
        } catch (EntityNotFoundException $ex) {
            throw new NotFoundHttpException($ex->getMessage());
        } catch (InvalidTransactionException $ex) {
            throw new UnauthorizedHttpException($ex->getMessage());
        }

        if (!($order->isValidated() || $order->isPending())) {
            return $this->render('SonataPaymentBundle:Payment:error.html.twig', [
                'order' => $order,
            ]);
        }

        return $this->render('SonataPaymentBundle:Payment:confirmation.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * this action redirect the user to the bank.
     *
     * @return Response
     */
    public function sendbankAction()
    {
        $basket = $this->getBasket();

        if ('POST' !== $this->getCurrentRequest()->getMethod()) {
            return $this->redirect($this->generateUrl('sonata_basket_index'));
        }

        if (!$basket->isValid()) {
            $this->get('session')->getFlashBag()->set(
                'error',
                $this->container->get('translator')->trans('basket_not_valid', [], 'SonataPaymentBundle')
            );

            return $this->redirect($this->generateUrl('sonata_basket_index'));
        }

        $payment = $basket->getPaymentMethod();

        // check if the basket is valid/compatible with the bank gateway
        if (!$payment->isBasketValid($basket)) {
            $this->get('session')->getFlashBag()->set(
                'error',
                $this->container->get('translator')->trans('basket_not_valid_with_current_payment_method', [], 'SonataPaymentBundle')
            );

            return $this->redirect($this->generateUrl('sonata_basket_index'));
        }

        // transform the basket into order
        $order = $this->getPaymentHandler()->getSendbankOrder($basket);
        $this->getBasketFactory()->reset($basket);

        // the payment must handle everything when calling the bank
        return $payment->sendbank($order);
    }

    /**
     * this action handler the callback sent from the bank.
     *
     * @throws NotFoundHttpException
     * @throws UnauthorizedHttpException
     *
     * @return Response
     */
    public function callbackAction()
    {
        try {
            $response = $this->getPaymentHandler()->getPaymentCallbackResponse($this->getCurrentRequest());
        } catch (EntityNotFoundException $ex) {
            throw new NotFoundHttpException($ex->getMessage());
        } catch (InvalidTransactionException $ex) {
            throw new UnauthorizedHttpException($ex->getMessage());
        }

        return $response;
    }

    /**
     * @return Response
     */
    public function termsAction()
    {
        return $this->render('SonataPaymentBundle:Payment:terms.html.twig');
    }

    /**
     * @return BasketFactoryInterface
     */
    protected function getBasketFactory()
    {
        return $this->get('sonata.basket.factory');
    }

    /**
     * @return Basket
     */
    protected function getBasket()
    {
        return $this->get('sonata.basket');
    }

    /**
     * @return PaymentHandlerInterface
     */
    protected function getPaymentHandler()
    {
        return $this->get('sonata.payment.handler');
    }

    /**
     * NEXT_MAJOR: Remove this method (inject Request $request into actions parameters).
     *
     * @return Request
     */
    private function getCurrentRequest()
    {
        return $this->container->get('request_stack')->getCurrentRequest();
    }
}
