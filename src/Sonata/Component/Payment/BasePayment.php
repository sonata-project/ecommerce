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

/**
 * A free delivery method, used this only for testing
 *
 */
abstract class BasePayment implements PaymentInterface
{

    protected $name;
    protected $code;
    protected $options;
    protected $router;
    protected $request;
    protected $transformer;
    protected $logger;


    /**
     * return status list
     *
     * @return array
     */
    public static function getStatusList() {
        return array(
            self::STATUS_OPEN              => 'open',
            self::STATUS_PENDING           => 'pending',
            self::STATUS_VALIDATED         => 'validated',
            self::STATUS_CANCELLED         => 'cancelled',
            self::STATUS_ERROR_VALIDATION  => 'error_validation',
            self::STATUS_WRONG_CALLBACK    => 'wrong_callback',
        );
    }


    /**
    * Generate a check value
    */
    public function generateUrlCheck($order) {

        return sha1(
            $order->getReference().
            $order->getCreatedAt("d/m/Y:G:i:s").
            $order->getId().
            $this->getOption('shop_url_key')
        );
    }

    public function getCode() {

        return $this->code;
    }

    public function setCode($code) {

        $this->code = $code;
    }

    public function getName() {

        return $this->name;
    }

    public function setName($name) {

        $this->name = $name;
    }

    public function setOptions($options) {

        $this->options = $options;
    }

    public function getOptions() {

        return $this->options;
    }

    public function getOption($name, $default = null) {

        return isset($this->options[$name]) ? $this->options[$name] : $default;
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

        return true;
    }

    /**
     * return true is the basket is valid for the current bank gateway
     *
     * @return boolean
     */
    public function isBasketValid(\Sonata\Component\Basket\Basket $basket) {
        
        return true;
    }

    /**
     *
     * @return boolean true if callback ok else false
     */
    public function isCallbackValid() {

        return false;
    }

    public function setLogger($logger) {

        $this->logger = $logger;
    }

    public function getLogger() {
        
        return $this->logger;
    }

    public function setRequest($request) {
        $this->request = $request;
    }

    public function getRequest() {
        return $this->request;
    }

    public function setRouter($router) {
        $this->router = $router;
    }

    public function getRouter() {
        return $this->router;
    }

    public function setTransformer($transformer) {
        $this->transformer = $transformer;
    }

    public function getTransformer() {
        return $this->transformer;
    }

}