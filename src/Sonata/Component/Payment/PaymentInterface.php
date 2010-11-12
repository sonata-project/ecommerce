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


    const BANK_CALL             = 1;
    const BANK_CALLBACK         = 2;

    const ERROR_ORDER_UNKNOWN   = -1;
    const ERROR_CALLBACK_KO     = -2;
    const ERROR_CALLBACK_OKKO   = -3;

    const STATUS_OPEN             = 0;  // created but not validated
    const STATUS_PENDING          = 1;  // the bank send a 'pending-like' status, so the payment is not validated, but the user payed
    const STATUS_VALIDATED        = 2;  // the bank confirm the payment
    const STATUS_CANCELLED        = 3;  // the user cancelled the payment
    const STATUS_ERROR_VALIDATION = 9;  // something wrong happen when the bank validate the postback
    const STATUS_WRONG_CALLBACK   = 10; // something wrong is sent from the bank. hack or the bank change something ...


    public function getName();

    public function getCode();


    /**
     * Send information to the bank, this method should handle
     * everything when called
     */
    public function callBank();

    /**
     *
     * @return boolean true if callback ok else false
     */
    public function isCallbackValid();

    /**
     * Load the order from the request
     *
     * @return
     */
    public function loadOrder();

    /**
     * Method called when an error occurs
     */
    public function handleError($code);

    /**
     * Send post back confirmation to the bank when the bank callback the site
     *
     * @return boolean true if ok
     */
    public function sendConfirmationReceipt($state);

    /**
     * Test if the request variables are valid for the current request
     *
     * WARNING : this methods does not check if the callback is valid
     *
     * @return boolean true if all parameter are ok
     */
    public function isRequestOk($order, $request);

    /**
     * return true is the basket is valid for the current bank gateway
     *
     * @return boolean
     */
    public function isBasketValid(\Sonata\Component\Basket\Basket $basket);

    /**
     * return true if the product can be added to the basket
     *
     * @param Basket $basket
     * @param Product $product
     */
    public function isAddableProduct(\Sonata\Component\Basket\Basket $basket, \Sonata\Component\Product\ProductInterface $product);

    /**
     * return errors from the current basket
     *
     * @return string
     */
    public function getErrorBasket();

    /**
     * return the transaction id from the bank
     *
     */
    public function getTransactionId();

    /**
     * encode value for the bank
     *
     * @param string $value
     * @return string the encoded value
     */
    public function encodeString($value);
}