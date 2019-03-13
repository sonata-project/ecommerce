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

namespace Sonata\PaymentBundle\Consumer;

use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Order\OrderManagerInterface;
use Sonata\Component\Payment\TransactionInterface;
use Sonata\Doctrine\Model\ManagerInterface;
use Sonata\NotificationBundle\Backend\BackendInterface;
use Sonata\NotificationBundle\Consumer\ConsumerEvent;
use Sonata\NotificationBundle\Consumer\ConsumerInterface;

/**
 * Consumer for Order processing.
 *
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
class PaymentProcessOrderConsumer implements ConsumerInterface
{
    /**
     * @var OrderManagerInterface
     */
    protected $orderManager;

    /**
     * @var ManagerInterface
     */
    protected $transactionManager;

    /**
     * @var BackendInterface
     */
    protected $backend;

    /**
     * @param OrderManagerInterface $orderManager
     * @param ManagerInterface      $transactionManager
     * @param BackendInterface      $backend
     */
    public function __construct(OrderManagerInterface $orderManager, ManagerInterface $transactionManager, BackendInterface $backend)
    {
        $this->orderManager = $orderManager;
        $this->transactionManager = $transactionManager;
        $this->backend = $backend;
    }

    public function process(ConsumerEvent $event): void
    {
        $order = $this->getOrder($event);
        $transaction = $this->getTransaction($event);

        $orderElements = $order->getOrderElements();

        foreach ($orderElements as $orderElement) {
            $this->backend->createAndPublish('sonata_payment_order_element_process', [
                'product_id' => $orderElement->getProductId(),
                'transaction_status' => $transaction->getStatusCode(),
                'order_status' => $order->getStatus(),
                'quantity' => $orderElement->getQuantity(),
                'product_type' => $orderElement->getProductType(),
            ]);
        }
    }

    /**
     * Get the related Order.
     *
     * @param ConsumerEvent $event
     *
     * @throws \RuntimeException
     *
     * @return OrderInterface
     */
    protected function getOrder(ConsumerEvent $event)
    {
        $orderId = $event->getMessage()->getValue('order_id');

        $order = $this->orderManager->getOrder($orderId);

        if (!$order) {
            throw new \RuntimeException(sprintf('Unable to retrieve Order %d', $orderId));
        }

        return $order;
    }

    /**
     * Get the related Transaction.
     *
     * @param ConsumerEvent $event
     *
     * @throws \RuntimeException
     *
     * @return TransactionInterface
     */
    protected function getTransaction(ConsumerEvent $event)
    {
        $transactionId = $event->getMessage()->getValue('transaction_id');

        $transaction = $this->transactionManager->findOneBy([
            'id' => $transactionId,
        ]);

        if (!$transaction) {
            throw new \RuntimeException(sprintf('Unable to retrieve Transaction %d', $transactionId));
        }

        return $transaction;
    }
}
