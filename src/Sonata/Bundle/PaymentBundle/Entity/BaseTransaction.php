<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\PaymentBundle\Entity;


use Sonata\Component\Payment\PaymentInterface;

/**
 * The Transaction class represents a callback request from the bank.
 *
 * The object contains :
 *   - the order            : the related order
 *   - the transaction_id   : the transaction token from the bank
 *   - the state            : the current state of the transaction, only OK or KO
 *   - the status           : the current status of the transaction
 *   - the error code       : the current error code of the transaction from the payment handler
 *
 */
class BaseTransaction 
{

    const STATE_OK = 1;
    const STATE_KO = 2;

    const STATUS_ORDER_UNKNOWN   = -1;  // the order is unknow
    const STATUS_OPEN             = 0;  // created but not validated
    const STATUS_PENDING          = 1;  // the bank send a 'pending-like' status, so the payment is not validated, but the user payed
    const STATUS_VALIDATED        = 2;  // the bank confirm the payment
    const STATUS_CANCELLED        = 3;  // the user cancelled the payment
    const STATUS_UNKNOWN          = 4;  // the bank sent a unknown code ...
    const STATUS_ERROR_VALIDATION = 9;  // something wrong happen when the bank validate the postback
    const STATUS_WRONG_CALLBACK   = 10; // something wrong is sent from the bank. hack or the bank change something ...
    const STATUS_WRONG_REQUEST    = 11; // the callback request is not valid

    protected $order;

    protected $transaction_id;

    protected $state;

    protected $parameters = array();

    protected $status_code;

    protected $created_at;

    protected $payment_code;

    public function setOrder($order)
    {
        $this->order = $order;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setTransactionId($transaction_id)
    {
        $this->transaction_id = $transaction_id;
    }

    public function getTransactionId()
    {
        return $this->transaction_id;
    }

    public function isValid() {
        return $this->state == self::STATE_OK;
    }

    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    public function getParameters()
    {

        return $this->parameters;
    }

    public function get($name, $default = null)
    {

        return isset($this->parameters[$name]) ? $this->parameters[$name] : $default;
    }

    public function setStatusCode($status_code)
    {
        $this->status_code = $status_code;
    }

    public function getStatusCode()
    {
        return $this->status_code;
    }

    /**
     * return status list
     *
     * @return array
     */
    public static function getStatusList()
    {
        return array(
            PaymentInterface::STATUS_ORDER_UNKNOWN     => 'order_unknown',
            PaymentInterface::STATUS_OPEN              => 'open',
            PaymentInterface::STATUS_PENDING           => 'pending',
            PaymentInterface::STATUS_VALIDATED         => 'validated',
            PaymentInterface::STATUS_CANCELLED         => 'cancelled',
            PaymentInterface::STATUS_ERROR_VALIDATION  => 'error_validation',
            PaymentInterface::STATUS_WRONG_CALLBACK    => 'wrong_callback',
        );
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setPaymentCode($payment_code)
    {
        $this->payment_code = $payment_code;
    }

    public function getPaymentCode()
    {
        return $this->payment_code;
    }
}