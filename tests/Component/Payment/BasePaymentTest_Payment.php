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

namespace Sonata\Component\Tests\Payment;

use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Payment\BasePayment;
use Sonata\Component\Payment\TransactionInterface;
use Sonata\Component\Product\ProductInterface;

class BasePaymentTest_Payment extends BasePayment
{
    /**
     * Send information to the bank, this method should handle
     * everything when called.
     *
     * @param \Sonata\Component\Order\OrderInterface $order
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sendbank(OrderInterface $order)
    {
        // TODO: Implement sendbank() method.
    }

    /**
     * @param \Sonata\Component\Payment\TransactionInterface $transaction
     *
     * @return bool true if callback ok else false
     */
    public function isCallbackValid(TransactionInterface $transaction)
    {
        // TODO: Implement isCallbackValid() method.
    }

    /**
     * Method called when an error occurs.
     *
     * @param \Sonata\Component\Payment\TransactionInterface $transaction
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleError(TransactionInterface $transaction)
    {
        // TODO: Implement handleError() method.
    }

    /**
     * Send post back confirmation to the bank when the bank callback the site.
     *
     * @param \Sonata\Component\Payment\TransactionInterface $transaction
     *
     * @return \Symfony\Component\HttpFoundation\Response, false otherwise
     */
    public function sendConfirmationReceipt(TransactionInterface $transaction)
    {
        // TODO: Implement sendConfirmationReceipt() method.
    }

    /**
     * Test if the request variables are valid for the current request.
     *
     * WARNING : this methods does not check if the callback is valid
     *
     * @param \Sonata\Component\Payment\TransactionInterface $transaction
     *
     * @return bool true if all parameter are ok
     */
    public function isRequestValid(TransactionInterface $transaction)
    {
        // TODO: Implement isRequestValid() method.
    }

    /**
     * return true is the basket is valid for the current bank gateway.
     *
     * @param \Sonata\Component\Basket\BasketInterface $basket
     *
     * @return bool
     */
    public function isBasketValid(BasketInterface $basket)
    {
        // TODO: Implement isBasketValid() method.
    }

    /**
     * return true if the product can be added to the basket.
     *
     * @param \Sonata\Component\Basket\BasketInterface   $basket
     * @param \Sonata\Component\Product\ProductInterface $product
     */
    public function isAddableProduct(BasketInterface $basket, ProductInterface $product)
    {
        // TODO: Implement isAddableProduct() method.
    }

    /**
     * return the transaction id from the bank.
     *
     * @param \Sonata\Component\Payment\TransactionInterface $transaction
     */
    public function applyTransactionId(TransactionInterface $transaction)
    {
        // TODO: Implement applyTransactionId() method.
    }

    /**
     * return the order reference from the transaction.
     *
     * @param \Sonata\Component\Payment\TransactionInterface $transaction
     *
     * @return string
     */
    public function getOrderReference(TransactionInterface $transaction)
    {
        // TODO: Implement getOrderReference() method.
    }
}
