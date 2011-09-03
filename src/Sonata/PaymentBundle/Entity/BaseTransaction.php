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

    public function __construct()
    {
        $this->createdAt     = new \DateTime;
        $this->transactionId = 'n/a';
    }

    /**
     * @param \Sonata\Component\Order\OrderInterface $order
     * @return void
     */
    public function setOrder(OrderInterface $order)
    {
        $this->order = $order;
    }

    /**
     * @return \Sonata\Component\Order\OrderInterface
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param int $state
     * @return void
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $transactionId
     * @return void
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->state == TransactionInterface::STATE_OK;
    }

    /**
     * @param array $parameters
     * @return void
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param $name
     * @param null $default
     * @return null
     */
    public function get($name, $default = null)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : $default;
    }

    /**
     * @param $statusCode
     * @return void
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return
     */
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
            TransactionInterface::STATUS_UNKNOWN           => 'status_unknow',
            TransactionInterface::STATUS_ERROR_VALIDATION  => 'error_validation',
            TransactionInterface::STATUS_WRONG_CALLBACK    => 'wrong_callback',
            TransactionInterface::STATUS_WRONG_REQUEST     => 'wrong_request',
            TransactionInterface::STATUS_ORDER_NOT_OPEN    => 'order_to_open',
        );
    }

    /**
     * @param \DateTime|null $createdAt
     * @return void
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param int $paymentCode
     * @return void
     */
    public function setPaymentCode($paymentCode)
    {
        $this->paymentCode = $paymentCode;
    }

    /**
     * @return
     */
    public function getPaymentCode()
    {
        return $this->paymentCode;
    }
}