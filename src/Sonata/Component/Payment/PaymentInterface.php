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

interface PaymentInterface {

    public function getName();

    public function getCode();


    /**
     * Send information to the bank, this method should handle
     * everything when called
     *
     * @return Response
     */
    public function callbank($order);

    /**
     *
     * @return boolean true if callback ok else false
     */
    public function isCallbackValid($transaction);

    /**
     * Method called when an error occurs
     *
     * @return Response 
     */
    public function handleError($transaction);

    /**
     * Send post back confirmation to the bank when the bank callback the site
     *
     * @return Response, false otherwise
     */
    public function sendConfirmationReceipt($transaction);

    /**
     * Test if the request variables are valid for the current request
     *
     * WARNING : this methods does not check if the callback is valid
     *
     * @return boolean true if all parameter are ok
     */
    public function isRequestValid($transaction);

    /**
     * return true is the basket is valid for the current bank gateway
     *
     * @return boolean
     */
    public function isBasketValid($basket);

    /**
     * return true if the product can be added to the basket
     *
     * @param Basket $basket
     * @param Product $product
     */
    public function isAddableProduct($basket, $product);

    /**
     * return the transaction id from the bank
     *
     */
    public function applyTransactionId($transaction);

    /**
     * encode value for the bank
     *
     * @param string $value
     * @return string the encoded value
     */
    public function encodeString($value);

    /**
     * return the order reference from the transaction
     * 
     * @abstract
     * @param  $transaction
     * @return string
     */
    public function getOrderReference($transaction);
}