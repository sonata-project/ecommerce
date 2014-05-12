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

use Doctrine\ORM\EntityNotFoundException;
use Sonata\Component\Payment\InvalidTransactionException;
use Sonata\Component\Payment\PaymentHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class PaymentController extends Controller
{
    /**
     * This action is called by the user after the sendbank
     * In most case the order is already cancelled by a previous callback
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function errorAction()
    {
        try {
            $order = $this->getPaymentHandler()->handleError($this->getRequest(), $this->getBasket());
        } catch (EntityNotFoundException $ex) {
            throw new NotFoundHttpException($ex->getMessage());
        } catch (InvalidTransactionException $ex) {
            throw new UnauthorizedHttpException($ex->getMessage());
        }

        return $this->render('SonataPaymentBundle:Payment:error.html.twig', array(
            'order' => $order,
        ));
    }

    /**
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function confirmationAction()
    {
        try {
            $order = $this->getPaymentHandler()->handleConfirmation($this->getRequest());
        } catch (EntityNotFoundException $ex) {
            throw new NotFoundHttpException($ex->getMessage());
        } catch (InvalidTransactionException $ex) {
            throw new UnauthorizedHttpException($ex->getMessage());
        }

        if (!($order->isValidated() || $order->isPending())) {
            return $this->render('SonataPaymentBundle:Payment:error.html.twig', array(
                'order' => $order,
            ));
        }

        return $this->render('SonataPaymentBundle:Payment:confirmation.html.twig', array(
            'order' => $order,
        ));
    }

    /**
     * this action redirect the user to the bank
     *
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function sendbankAction()
    {
        $basket = $this->getBasket();

        if ($this->get('request')->getMethod() !== 'POST') {
            return $this->redirect($this->generateUrl('sonata_basket_index'));
        }

        if (!$basket->isValid()) {
            $this->get('session')->getFlashBag()->set(
                'error',
                $this->container->get('translator')->trans('basket_not_valid', array(), 'SonataPaymentBundle')
            );

            return $this->redirect($this->generateUrl('sonata_basket_index'));
        }

        $payment = $basket->getPaymentMethod();

        // check if the basket is valid/compatible with the bank gateway
        if (!$payment->isBasketValid($basket)) {
            $this->get('session')->getFlashBag()->set(
                'error',
                $this->container->get('translator')->trans('basket_not_valid_with_current_payment_method', array(), 'SonataPaymentBundle')
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
     * this action handler the callback sent from the bank
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     *
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function callbackAction()
    {
        try {
            $response = $this->getPaymentHandler()->getPaymentCallbackResponse($this->getRequest());
        } catch (EntityNotFoundException $ex) {
            throw new NotFoundHttpException($ex->getMessage());
        } catch (InvalidTransactionException $ex) {
            throw new UnauthorizedHttpException($ex->getMessage());
        }

        return $response;
    }

    public function termsAction()
    {
        return $this->render('SonataPaymentBundle:Payment:terms.html.twig');
    }

    /**
     * @return \Sonata\Component\Basket\BasketFactoryInterface
     */
    protected function getBasketFactory()
    {
        return $this->get('sonata.basket.factory');
    }

    /**
     * @return \Sonata\Component\Basket\Basket
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
}
