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

use Application\PaymentBundle\Entity\Transaction;

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

    protected $transformers;

    protected $logger;

    protected $is_debug;

    protected $enabled;
    
    protected $translator;

    /**
    * Generate a check value
    */
    public function generateUrlCheck($order)
    {

        return sha1(
            $order->getReference().
            $order->getCreatedAt()->format("d/m/Y:G:i:s").
            $order->getId().
            $this->getOption('shop_url_key')
        );
    }

    public function getCode()
    {

        return $this->code;
    }

    public function setCode($code)
    {

        $this->code = $code;
    }

    public function getName()
    {

        return $this->name;
    }

    public function setName($name)
    {

        $this->name = $name;
    }

    public function setOptions($options)
    {

        $this->options = $options;
    }

    public function getOptions()
    {

        return $this->options;
    }

    public function getOption($name, $default = null)
    {

        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

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
     * return true if the product can be added to the basket
     *
     * @param Basket $basket
     * @param Product $product
     */
    public function isAddableProduct($basket, $product)
    {

        return true;
    }

    /**
     * return true is the basket is valid for the current bank gateway
     *
     * @return boolean
     */
    public function isBasketValid($basket)
    {
        
        return true;
    }

    /**
     *
     * @return boolean true if callback ok else false
     */
    public function isCallbackValid($transaction)
    {

        return false;
    }

    public function setLogger($logger)
    {

        $this->logger = $logger;
    }

    public function getLogger()
    {
        
        return $this->logger;
    }

    public function setRouter($router)
    {
        $this->router = $router;
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function addTransformer($id, $transformer)
    {

        $this->transformers[$id] = $transformer;

    }

    public function getTransformer($name)
    {

        return isset($this->transformers[$name]) ? $this->transformers[$name] : false;
    }

    public function callback($transaction)
    {

        // check if the order exists
        if(!$transaction->getOrder()) {
            $transaction->setStatusCode(Transaction::STATUS_ORDER_UNKNOWN);
            $transaction->setState(Transaction::STATE_KO);

            return $this->handleError($transaction);
        }

        // check if the request is valid
        if(!$this->isRequestValid($transaction)) {
            $transaction->setStatusCode(Transaction::STATUS_WRONG_REQUEST);
            $transaction->setState(Transaction::STATE_KO);

            return $this->handleError($transaction);
        }

        // check if the callback is valid
        if(!$this->isCallbackValid($transaction)) {
            $transaction->setStatusCode(Transaction::STATUS_WRONG_CALLBACK);
            $transaction->setState(Transaction::STATE_KO);

            return $this->handleError($transaction);
        }

        // apply the transaction id and define the order to the transaction object
        $this->applyTransactionId($transaction);

        // send the confirmation request to the bank
        if(!($response = $this->sendConfirmationReceipt($transaction))) {

            $response = $this->handleError($transaction);

            $transaction->getOrder()->setStatus($transaction->getStatus());

        } else {

            $transaction->getOrder()->setStatus($transaction->getStatus());
            $transaction->getOrder()->setValidatedAt(new \Datetime);
        }

        return $response;
    }

    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    public function getTranslator()
    {
        return $this->translator;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }
}