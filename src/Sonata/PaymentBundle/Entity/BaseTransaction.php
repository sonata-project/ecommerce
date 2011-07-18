<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\PaymentBundle\Entity;

use Sonata\Component\Payment\TransactionInterface;
use Sonata\Component\Payment\PaymentInterface;
use Sonata\Component\Order\OrderInterface;

/**
 * The Transaction class represents a callback request from the bank.
 *
 * The object contains :
 *   - the order            : the related order
 *   - the transactionId    : the transaction token from the bank
 *   - the state            : the current state of the transaction, only OK or KO
 *   - the status           : the current status of the transaction
 *   - the error code       : the current error code of the transaction from the payment handler
 *
 */
class BaseTransaction implements TransactionInterface
{
    protected $order;

    protected $transactionId;

    protected $state;

    protected $parameters = array();

    protected $statusCode;

    protected $createdAt;

    protected $paymentCode;

    public function setOrder(OrderInterface $order)
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

    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    public function getTransactionId()
    {
        return $this->transactionId;
    }

    public function isValid() {
        return $this->state == TransactionInterface::STATE_OK;
    }

    public function setParameters(array $parameters)
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

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * return status list
     *
     * @return array
     */
    public static function getStatusList()
    {
        return array(
            TransactionInterface::STATUS_ORDER_UNKNOWN     => 'order_unknown',
            TransactionInterface::STATUS_OPEN              => 'open',
            TransactionInterface::STATUS_PENDING           => 'pending',
            TransactionInterface::STATUS_VALIDATED         => 'validated',
            TransactionInterface::STATUS_CANCELLED         => 'cancelled',
            TransactionInterface::STATUS_ERROR_VALIDATION  => 'error_validation',
            TransactionInterface::STATUS_WRONG_CALLBACK    => 'wrong_callback',
        );
    }

    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setPaymentCode($paymentCode)
    {
        $this->paymentCode = $paymentCode;
    }

    public function getPaymentCode()
    {
        return $this->paymentCode;
    }
}