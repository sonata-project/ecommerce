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

namespace Sonata\Component\Payment;

use Doctrine\ORM\EntityNotFoundException;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Event\PaymentEvent;
use Sonata\Component\Event\PaymentEvents;
use Sonata\Component\Generator\ReferenceInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Order\OrderManagerInterface;
use Sonata\NotificationBundle\Backend\BackendInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Responsible for interactions between PaymentController & Model.
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class PaymentHandler implements PaymentHandlerInterface
{
    /**
     * @var OrderManagerInterface
     */
    protected $orderManager;

    /**
     * @var ReferenceInterface
     */
    protected $referenceGenerator;

    /**
     * @var TransactionManagerInterface
     */
    protected $transactionManager;

    /**
     * @var PaymentSelectorInterface
     */
    protected $paymentSelector;

    /**
     * @var BackendInterface
     */
    protected $notificationBackend;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param OrderManagerInterface       $orderManager
     * @param PaymentSelectorInterface    $paymentSelector
     * @param ReferenceInterface          $referenceGenerator
     * @param TransactionManagerInterface $transactionManager
     * @param BackendInterface            $notificationBackend
     * @param EventDispatcherInterface    $eventDispatcher
     */
    public function __construct(OrderManagerInterface $orderManager, PaymentSelectorInterface $paymentSelector, ReferenceInterface $referenceGenerator, TransactionManagerInterface $transactionManager, BackendInterface $notificationBackend, EventDispatcherInterface $eventDispatcher)
    {
        $this->orderManager = $orderManager;
        $this->paymentSelector = $paymentSelector;
        $this->referenceGenerator = $referenceGenerator;
        $this->transactionManager = $transactionManager;
        $this->notificationBackend = $notificationBackend;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function handleError(Request $request, BasketInterface $basket)
    {
        // retrieve the transaction
        $transaction = $this->createTransaction($request);
        $order = $this->getValidOrder($transaction);

        $event = new PaymentEvent($order, $transaction);
        $this->getEventDispatcher()->dispatch(PaymentEvents::PRE_ERROR, $event);

        if ($order->isCancellable()) {
            $order->setStatus(OrderInterface::STATUS_STOPPED);
        }

        $transaction->setState(TransactionInterface::STATE_OK);
        $transaction->setStatusCode(TransactionInterface::STATUS_CANCELLED);

        // save the payment transaction
        $this->transactionManager->save($transaction);
        $this->orderManager->save($order);

        // rebuilt from the order information
        $this->getPayment($transaction->getPaymentCode())->getTransformer('order')->transformIntoBasket($order, $basket);

        $event = new PaymentEvent($order, $transaction);
        $this->getEventDispatcher()->dispatch(PaymentEvents::POST_ERROR, $event);

        $this->notificationBackend->createAndPublish('sonata_payment_order_process', [
            'order_id' => $order->getId(),
            'transaction_id' => $transaction->getId(),
        ]);

        return $order;
    }

    public function handleConfirmation(Request $request)
    {
        $transaction = $this->createTransaction($request);
        $order = $this->getValidOrder($transaction);

        $event = new PaymentEvent($order, $transaction);
        $this->getEventDispatcher()->dispatch(PaymentEvents::CONFIRMATION, $event);

        return $order;
    }

    public function getSendbankOrder(BasketInterface $basket)
    {
        $order = $basket->getPaymentMethod()->getTransformer('basket')->transformIntoOrder($basket);

        $event = new PaymentEvent($order);
        $this->getEventDispatcher()->dispatch(PaymentEvents::PRE_SENDBANK, $event);

        // save the order
        $this->orderManager->save($order);

        // assign correct reference number
        $this->referenceGenerator->order($order);

        $event = new PaymentEvent($order);
        $this->getEventDispatcher()->dispatch(PaymentEvents::POST_SENDBANK, $event);

        return $order;
    }

    public function getPaymentCallbackResponse(Request $request)
    {
        // retrieve the transaction
        $transaction = $this->createTransaction($request);
        $order = $this->getValidOrder($transaction);

        $event = new PaymentEvent($order, $transaction);
        $this->getEventDispatcher()->dispatch(PaymentEvents::PRE_CALLBACK, $event);

        // start the payment callback
        $response = $this->getPayment($transaction->getPaymentCode())->callback($transaction);

        $this->transactionManager->save($transaction);
        $this->orderManager->save($order);

        $event = new PaymentEvent($order, $transaction, $response);
        $this->getEventDispatcher()->dispatch(PaymentEvents::POST_CALLBACK, $event);

        $this->notificationBackend->createAndPublish('sonata_payment_order_process', [
            'order_id' => $order->getId(),
            'transaction_id' => $transaction->getId(),
        ]);

        return $response;
    }

    /**
     * @return EventDispatcherInterface
     */
    protected function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * Retrieves the order matching $transaction and adds it to $transaction.
     *
     * @param TransactionInterface $transaction The request's transaction (will be linked to the order in the process)
     *
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws InvalidTransactionException
     *
     * @return \Sonata\Component\Order\OrderInterface
     */
    protected function getValidOrder(TransactionInterface $transaction)
    {
        $payment = $this->getPayment($transaction->getPaymentCode());

        // retrieve the related order
        $reference = $payment->getOrderReference($transaction);

        if (!$reference) {
            throw new InvalidTransactionException();
        }

        $order = $this->orderManager->findOneby([
            'reference' => $reference,
        ]);

        if (!$order) {
            throw new EntityNotFoundException(sprintf('Order %s', $reference));
        }

        $transaction->setOrder($order);

        // control the handshake value
        if (!$payment->isRequestValid($transaction)) {
            throw new InvalidTransactionException($order->getReference());
        }

        return $order;
    }

    /**
     * @param Request $request
     *
     * @throws PaymentNotFoundException
     *
     * @return \Sonata\Component\Payment\TransactionInterface
     */
    protected function createTransaction(Request $request)
    {
        $payment = $this->getPayment($request->get('bank'));

        $transaction = $this->transactionManager->create();
        $transaction->setPaymentCode($payment->getCode());
        $transaction->setParameters(array_replace($request->query->all(), $request->request->all()));

        return $transaction;
    }

    /**
     * @param $code
     *
     * @return PaymentInterface
     */
    protected function getPayment($code)
    {
        return $this->paymentSelector->getPayment($code);
    }
}
