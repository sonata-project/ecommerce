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

use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Transformer\BaseTransformer;
use Symfony\Component\HttpFoundation\Response;

interface PaymentInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getCode();

    /**
     * Send information to the bank, this method should handle
     * everything when called.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sendbank(OrderInterface $order);

    /**
     * @return Response
     */
    public function callback(TransactionInterface $transaction);

    /**
     * @param \Sonata\Component\Payment\TransactionInterface $transaction
     *
     * @return bool true if callback ok else false
     */
    public function isCallbackValid(TransactionInterface $transaction);

    /**
     * Method called when an error occurs.
     *
     * @param \Sonata\Component\Payment\TransactionInterface $transaction
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleError(TransactionInterface $transaction);

    /**
     * Send post back confirmation to the bank when the bank callback the site.
     *
     * @param \Sonata\Component\Payment\TransactionInterface $transaction
     *
     * @return \Symfony\Component\HttpFoundation\Response, false otherwise
     */
    public function sendConfirmationReceipt(TransactionInterface $transaction);

    /**
     * Test if the request variables are valid for the current request.
     *
     * WARNING : this methods does not check if the callback is valid
     *
     * @param \Sonata\Component\Payment\TransactionInterface $transaction
     *
     * @return bool true if all parameter are ok
     */
    public function isRequestValid(TransactionInterface $transaction);

    /**
     * return true is the basket is valid for the current bank gateway.
     *
     * @return bool
     */
    public function isBasketValid(BasketInterface $basket);

    /**
     * return true if the product can be added to the basket.
     */
    public function isAddableProduct(BasketInterface $basket, ProductInterface $product);

    /**
     * return the transaction id from the bank.
     *
     * @param \Sonata\Component\Payment\TransactionInterface $transaction
     */
    public function applyTransactionId(TransactionInterface $transaction);

    /**
     * encode value for the bank.
     *
     * @param string $value
     *
     * @return string the encoded value
     */
    public function encodeString($value);

    /**
     * return the order reference from the transaction.
     *
     * @param \Sonata\Component\Payment\TransactionInterface $transaction
     *
     * @return string
     */
    public function getOrderReference(TransactionInterface $transaction);

    /**
     * Gets the transformer for $name.
     *
     * @param $name
     *
     * @return BaseTransformer
     */
    public function getTransformer($name);
}
