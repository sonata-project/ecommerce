<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\Tests\Component\Basket;

use Sonata\Component\Payment\BasePayment;

class Payment extends BasePayment {
    public function isAddressRequired() {
        return true;
    }

    public function getName() {
        return "delivery 1";
    }

    public function getVat() {
        return 19.60;
    }

    public function getPrice() {
        return 120;
    }

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
        // TODO: Implement getTransactionId() method.
    }

    /**
     * return errors from the current basket
     *
     * @return string
     */
    public function getErrorBasket() {
        // TODO: Implement getErrorBasket() method.
    }

    /**
     * return true if the product can be added to the basket
     *
     * @param Basket $basket
     * @param Product $product
     */
    public function isAddableProduct(\Sonata\Component\Basket\Basket $basket, \Sonata\Component\Product\ProductInterface $product) {
        // TODO: Implement isAddableProduct() method.
    }

    /**
     * return true is the basket is valid for the current bank gateway
     *
     * @return boolean
     */
    public function isBasketValid(\Sonata\Component\Basket\Basket $basket) {
        // TODO: Implement isBasketValid() method.
    }

    /**
     * Test if the request variables are valid for the current request
     *
     * WARNING : this methods does not check if the callback is valid
     *
     * @return boolean true if all parameter are ok
     */
    public function isRequestOk($order, $request) {
        // TODO: Implement isRequestOk() method.
    }

    /**
     * Send post back confirmation to the bank when the bank callback the site
     *
     * @return boolean true if ok
     */
    public function sendConfirmationReceipt($state) {
        // TODO: Implement sendConfirmationReceipt() method.
    }

    /**
     * Method called when an error occurs
     */
    public function handleError($code) {
        // TODO: Implement handleError() method.
    }

    /**
     * Load the order from the request
     *
     * @return
     */
    public function loadOrder() {
        // TODO: Implement loadOrder() method.
    }

    /**
     *
     * @return boolean true if callback ok else false
     */
    public function isCallbackValid() {
        // TODO: Implement isCallbackValid() method.
    }

    /**
     * Send information to the bank, this method should handle
     * everything when called
     */
    public function callBank() {
        // TODO: Implement callBank() method.
    }

    public function getCode() {
        // TODO: Implement getCode() method.
    }



}
