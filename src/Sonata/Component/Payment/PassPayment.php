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


class PassPayment extends BasePayment
{
    /**
     * encode value for the bank
     *
     * @param string $value
     * @return string the encoded value
     */
    public function encodeString($value) {
        return $value;
    }

    /**
     * return the transaction id from the bank
     *
     */
    public function getTransactionId() {
        return 1;
    }

    /**
     * return true if the product can be added to the basket
     *
     * @param Basket $basket
     * @param Product $product
     */
    public function isAddableProduct($basket, $product) {

        return true;
    }

    /**
     * return true is the basket is valid for the current bank gateway
     *
     * @return boolean
     */
    public function isBasketValid( $basket) {

        return true;
    }

    /**
     * Test if the request variables are valid for the current request
     *
     * WARNING : this methods does not check if the callback is valid
     *
     * @return boolean true if all parameter are ok
     */
    public function isRequestValid($transaction) {

        return true;
    }

    /**
     * Send post back confirmation to the bank when the bank callback the site
     *
     * @return boolean true if ok
     */
    public function sendConfirmationReceipt($state) {
        
    }

    /**
     * Method called when an error occurs
     */
    public function handleError($code) {

    }

    /**
     *
     * @return boolean true if callback ok else false
     */
    public function isCallbackValid($transaction) {
        return true;
    }

    /**
     * Send information to the bank, this method should handle
     * everything when called
     */
    public function callbank($order) {

        $response = new Symfony\Component\HttpFoundation\Response;
        $response->setRedirect($this->router->generateUrl('url_return_ok'));
        $response->setPrivate(true);

    }

    /**
     * return the order reference from the transaction
     *
     * @param  $transaction
     * @return string
     */
    public function getOrderReference($transaction) {

        return $transaction->get('reference');
    }

    /**
     * return the transaction id from the bank
     *
     */
    public function applyTransactionId($transaction) {

        $transaction->setTransactionId($transaction->get('transaction_id'));
    }
}