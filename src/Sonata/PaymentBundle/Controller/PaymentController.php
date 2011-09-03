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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\Component\Payment\TransactionInterface;
use Sonata\Component\Payment\PaymentInterface;
use Sonata\Component\Order\OrderInterface;

class PaymentController extends Controller
{
    /**
     * This action is called by the user after the callbank
     * In most case the order is already cancelled by a previous callback
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function errorAction()
    {
        // retrieve the payment handler
        $payment    = $this->getPaymentHandler();

        // retrieve the transaction
        $transaction = $this->createTransaction($payment);

        // retrieve the related order
        $reference  = $payment->getOrderReference($transaction);

        $order      = $this->getOrderManager()->findOneby(array(
            'reference' => $reference
        ));

        if (!$order) {
            throw new NotFoundHttpException(sprintf('Order %s', $reference));
        }

        $transaction->setOrder($order);

        // control the handshake value
        if (!$payment->isRequestValid($transaction)) {
            throw new NotFoundHttpException(sprintf('Invalid check - Order %s', $reference));
        }

        if ($order->isCancellable()) {
            $order->setStatus(OrderInterface::STATUS_STOPPED);
        }

        $transaction->setState(TransactionInterface::STATE_OK);
        $transaction->setStatusCode(TransactionInterface::STATUS_CANCELLED);

        // save the payment transaction
        $this->getTransactionManager()->save($transaction);
        $this->getOrderManager()->save($order);

        // rebuilt from the order information
        $payment->getTransformer('order')->transformIntoBasket($order, $this->get('sonata.basket'));

        return $this->render('SonataPaymentBundle:Payment:error.html.twig', array(
            'order' => $order,
        ));
    }

    public function confirmationAction()
    {
        $payment = $this->getPaymentHandler();
        $transaction = $this->createTransaction($payment);

        $reference = $payment->getOrderReference($transaction);

        if (!$payment->isRequestValid($transaction)) {
            throw new NotFoundHttpException(sprintf('Invalid check - Order %s', $reference));
        }

        $order = $this->getOrderManager()->findOneBy(array(
            'reference' => $reference
        ));

        if (!$order) {
            throw new NotFoundHttpException(sprintf('Order %s', $reference));
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
     *
     * this action redirect the user to the bank
     *
     * @return Response
     */
    public function callbankAction()
    {
        $basket     = $this->get('sonata.basket');
        $request    = $this->get('request');

        if ($request->getMethod() !== 'POST') {
            return $this->redirect($this->generateUrl('sonata_basket_index'));
        }

        if (!$basket->isValid()) {
            $this->get('session')->setFlash(
                'error',
                $this->container->get('translator')->trans('basket_not_valid', array(), 'SonataPaymentBundle')
            );

            return $this->redirect($this->generateUrl('sonata_basket_index'));
        }

        $payment = $basket->getPaymentMethod();

        // check if the basket is valid/compatible with the bank gateway
        if (!$payment->isBasketValid($basket)) {
            $this->get('session')->setFlash(
                'error',
                $this->container->get('translator')->trans('basket_not_valid_with_current_payment_method', array(), 'SonataPaymentBundle')
            );

            return $this->redirect($this->generateUrl('sonata_basket_index'));
        }

        // transform the basket into order
        $order = $payment->getTransformer('basket')->transformIntoOrder($basket);

        // save the order
        $this->get('sonata.order.manager')->save($order);

        // assign correct reference number
        $this->get('sonata.generator')->order($order);

        $basket->reset();

        // the payment must handle everything when calling the bank
        return $payment->callbank($order);
    }

    /**
     * this action handler the callback sent from the bank
     *
     * @return Response
     */
    public function callbackAction()
    {
        // retrieve the payment handler
        $payment = $this->getPaymentHandler();

        // build the transaction
        $transaction = $this->createTransaction($payment);

        // retrieve the related order
        $reference  = $payment->getOrderReference($transaction);

        $order = $this->getOrderManager()->findOneBy(array(
            'reference' => $reference
        ));

        if (!$order instanceof OrderInterface) {
            throw new NotFoundHttpException(sprintf('Unable to find the Order %s', $reference));
        }

        $transaction->setOrder($order);

        // check if the callback is valid
        if (!$payment->isCallbackValid($transaction)) {
            // ask the payment handler the error
            return $payment->handleError($transaction);
        }

        $response = $payment->sendConfirmationReceipt($transaction);

        if ($transaction->getState() == TransactionInterface::STATE_KO) {
            return $payment->handleError($transaction);
        }

        $this->getTransactionManager()->save($transaction);
        $this->getOrderManager()->save($transaction->getOrder());

        return $response;
    }

    /**
     * @param \Sonata\Component\Payment\PaymentInterface $payment
     * @return \Sonata\Component\Payment\TransactionInterface
     */
    public function createTransaction(PaymentInterface $payment)
    {
        $transaction = $this->get('sonata.transaction.manager')->create();
        $transaction->setPaymentCode($payment->getCode());
        $transaction->setCreatedAt(new \DateTime);
        $transaction->setParameters(array_replace($this->getRequest()->query->all(), $this->getRequest()->request->all()));

        $payment->applyTransactionId($transaction);

        return $transaction;
    }

    /**
     * @return object|\Sonata\Component\Payment\PaymentInterface
     */
    public function getPaymentHandler()
    {
        $payment = $this->get(sprintf('sonata.payment.method.%s', $this->getRequest()->get('bank')));

        if (!$payment instanceof PaymentInterface) {
            throw new NotFoundHttpException();
        }

        return $payment;
    }

    /**
     * @return object|\Sonata\Component\Order\OrderManagerInterface
     */
    public function getOrderManager()
    {
        return $this->get('sonata.order.manager');
    }

    /**
     * @return object|\Sonata\Component\Payment\TransactionManagerInterface
     */
    public function getTransactionManager()
    {
        return $this->get('sonata.transaction.manager');
    }
}