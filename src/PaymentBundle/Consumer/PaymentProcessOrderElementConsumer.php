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
use Sonata\Component\Payment\TransactionInterface;
use Sonata\Component\Product\Pool as ProductPool;
use Sonata\Component\Product\ProductProviderInterface;
use Sonata\Doctrine\Model\ManagerInterface;
use Sonata\NotificationBundle\Consumer\ConsumerEvent;
use Sonata\NotificationBundle\Consumer\ConsumerInterface;

/**
 * Consumer for OrderElement processing.
 *
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
class PaymentProcessOrderElementConsumer implements ConsumerInterface
{
    /**
     * @var ManagerInterface
     */
    protected $orderElementManager;

    /**
     * @var ProductPool
     */
    protected $productPool;

    /**
     * @param ManagerInterface $orderElementManager
     * @param ProductPool      $productPool
     */
    public function __construct(ManagerInterface $orderElementManager, ProductPool $productPool)
    {
        $this->orderElementManager = $orderElementManager;
        $this->productPool = $productPool;
    }

    public function process(ConsumerEvent $event): void
    {
        $orderStatus = $event->getMessage()->getValue('order_status');
        $transactionStatus = $event->getMessage()->getValue('transaction_status');
        $productId = $event->getMessage()->getValue('product_id');
        $productType = $event->getMessage()->getValue('product_type');
        $quantity = $event->getMessage()->getValue('quantity');

        $productManager = $this->getProductManager($productType);
        $productProvider = $this->getProductProvider($productType);

        $diff = $this->generateDiffValue($transactionStatus, $orderStatus, $quantity);

        if ($diff) {
            $productProvider->updateStock($productId, $productManager, $diff);
        }
    }

    /**
     * Calculate diff value for Product stock update (base on Transaction and Order statuses).
     *
     * @param int $transactionStatus
     * @param int $orderStatus
     * @param int $quantity
     *
     * @return int
     */
    public function generateDiffValue($transactionStatus, $orderStatus, $quantity)
    {
        if (TransactionInterface::STATUS_VALIDATED === $transactionStatus && OrderInterface::STATUS_VALIDATED === $orderStatus) {
            return -1 * $quantity;
        }
    }

    /**
     * Get the ProductProvider of the given Product.
     *
     * @param string $productType
     *
     * @return ProductProviderInterface
     */
    protected function getProductProvider($productType)
    {
        return $this->productPool->getProvider($productType);
    }

    /**
     * Get the Manager of the given Product.
     *
     * @param string $productType
     *
     * @return ManagerInterface
     */
    protected function getProductManager($productType)
    {
        return $this->productPool->getManager($productType);
    }
}
