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

use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Payment\TransactionInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Product\ProductInterface;

interface PaymentInterface
{

    function getName();

    function getCode();

    /**
     * Send information to the bank, this method should handle
     * everything when called
     *
     * @return Response
     */
    function callbank(OrderInterface $order);

    /**
     *
     * @param TransactionInterface $transaction
     * @return boolean true if callback ok else false
     */
    function isCallbackValid(TransactionInterface $transaction);

    /**
     * Method called when an error occurs
     *
     * @param TransactionInterface $transaction
     * @return Response 
     */
    function handleError(TransactionInterface $transaction);

    /**
     * Send post back confirmation to the bank when the bank callback the site
     *
     * @param TransactionInterface $transaction
     * @return Response, false otherwise
     */
    function sendConfirmationReceipt(TransactionInterface $transaction);

    /**
     * Test if the request variables are valid for the current request
     *
     * WARNING : this methods does not check if the callback is valid
     *
     * @param TransactionInterface $transaction
     * @return boolean true if all parameter are ok
     */
    function isRequestValid(TransactionInterface $transaction);

    /**
     * return true is the basket is valid for the current bank gateway
     *
     * @param TransactionInterface $transaction
     * @return boolean
     */
    function isBasketValid(BasketInterface $basket);

    /**
     * return true if the product can be added to the basket
     *
     * @param BasketInterface $basket
     * @param ProductInterface $product
     */
    function isAddableProduct(BasketInterface $basket, ProductInterface $product);

    /**
     * return the transaction id from the bank
     * 
     * @param TransactionInterface $transaction
     */
    function applyTransactionId(TransactionInterface $transaction);

    /**
     * encode value for the bank
     *
     * @param string $value
     * @return string the encoded value
     */
    function encodeString($value);

    /**
     * return the order reference from the transaction
     * 
     * @abstract
     * @param TransactionInterface $transaction
     * @return string
     */
    function getOrderReference(TransactionInterface $transaction);
}