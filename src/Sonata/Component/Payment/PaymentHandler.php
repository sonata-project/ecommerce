<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Payment;

use Doctrine\ORM\EntityNotFoundException;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Generator\ReferenceInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Order\OrderManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PaymentHandler
 *
 * Responsible for interactions between PaymentController & Model
 *
 * @package Sonata\Component\Payment
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
     * Constructor
     *
     * @param OrderManagerInterface       $orderManager
     * @param PaymentSelectorInterface    $paymentSelector
     * @param ReferenceInterface          $referenceGenerator
     * @param TransactionManagerInterface $transactionManager
     */
    public function __construct(OrderManagerInterface $orderManager, PaymentSelectorInterface $paymentSelector, ReferenceInterface $referenceGenerator, TransactionManagerInterface $transactionManager)
    {
        $this->orderManager       = $orderManager;
        $this->paymentSelector    = $paymentSelector;
        $this->referenceGenerator = $referenceGenerator;
        $this->transactionManager = $transactionManager;
    }

    /**
     * {@inheritdoc}
     */
    public function handleError(Request $request, BasketInterface $basket)
    {
        // retrieve the transaction
        $transaction = $this->createTransaction($request);

        $order = $this->getValidOrder($transaction);

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

        return $order;
    }

    /**
     * {@inheritdoc}
     */
    public function handleConfirmation(Request $request)
    {
        $transaction = $this->createTransaction($request);

        return $this->getValidOrder($transaction);
    }

    /**
     * {@inheritdoc}
     */
    public function getSendbankOrder(BasketInterface $basket)
    {
        $order = $basket->getPaymentMethod()->getTransformer('basket')->transformIntoOrder($basket);

        // save the order
        $this->orderManager->save($order);

        // assign correct reference number
        $this->referenceGenerator->order($order);

        $basket->reset();

        return $order;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentCallbackResponse(Request $request)
    {
        // retrieve the transaction
        $transaction = $this->createTransaction($request);

        $order = $this->getValidOrder($transaction);

        // start the payment callback
        $response = $this->getPayment($transaction->getPaymentCode())->callback($transaction);

        $this->transactionManager->save($transaction);
        $this->orderManager->save($order);

        return $response;
    }

    /**
     * Retrieves the order matching $transaction and adds it to $transaction
     *
     * @param TransactionInterface $transaction The request's transaction (will be linked to the order in the process)
     *
     * @return \Sonata\Component\Order\OrderInterface
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \InvalidTransactionException
     */
    protected function getValidOrder(TransactionInterface $transaction)
    {
        $payment = $this->getPayment($transaction->getPaymentCode());

        // retrieve the related order
        $reference  = $payment->getOrderReference($transaction);

        if (!$reference) {
            throw new InvalidTransactionException();
        }

        $order = $this->orderManager->findOneby(array(
            'reference' => $reference
        ));

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
     * @param         $bank
     *
     * @return \Sonata\Component\Payment\TransactionInterface
     *
     * @throws PaymentNotFoundException
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
