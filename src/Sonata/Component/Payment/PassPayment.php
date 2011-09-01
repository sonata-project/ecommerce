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

use Symfony\Component\HttpFoundation\Response;
use Sonata\Component\Payment\PaymentInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Payment\TransactionInterface;

class PassPayment extends BasePayment
{
    /**
     * encode value for the bank
     *
     * @param string $value
     * @return string the encoded value
     */
    public function encodeString($value)
    {
        return $value;
    }

    /**
     * @return int
     */
    public function getTransactionId()
    {
        return 1;
    }

    /**
     * @param \Sonata\Component\Basket\BasketInterface $basket
     * @param \Sonata\Component\Product\ProductInterface $product
     * @return bool
     */
    public function isAddableProduct(BasketInterface $basket, ProductInterface $product)
    {
        return true;
    }

    /**
     * @param \Sonata\Component\Basket\BasketInterface $basket
     * @return bool
     */
    public function isBasketValid(BasketInterface $basket)
    {
        return true;
    }

    /**
     * @param TransactionInterface $transaction
     * @return bool
     */
    public function isRequestValid(TransactionInterface $transaction)
    {
        return true;
    }

    /**
     * @param TransactionInterface $transaction
     * @return void
     */
    public function sendConfirmationReceipt(TransactionInterface $transaction)
    {

    }

    /**
     * @param TransactionInterface $transaction
     * @return void
     */
    public function handleError(TransactionInterface $transaction)
    {

    }

    /**
     * @param TransactionInterface $transaction
     * @return bool
     */
    public function isCallbackValid(TransactionInterface $transaction)
    {
        return true;
    }

    /**
     * @param \Sonata\Component\Order\OrderInterface $order
     * @return void
     */
    public function callbank(OrderInterface $order)
    {
        $response = new Response;
        $response->setRedirect($this->router->generateUrl('url_return_ok'));
        $response->setPrivate(true);
    }

    /**
     * @param TransactionInterface $transaction
     * @return string
     */
    public function getOrderReference(TransactionInterface $transaction)
    {
        return $transaction->get('reference');
    }

    /**
     * @param TransactionInterface $transaction
     * @return void
     */
    public function applyTransactionId(TransactionInterface $transaction)
    {
        $transaction->setTransactionId($transaction->get('transaction_id'));
    }
}