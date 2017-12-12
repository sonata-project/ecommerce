<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Payment;

use Sonata\Component\Order\OrderInterface;

/**
 * The Transaction class represents a callback request from the bank.
 *
 * The object contains :
 *   - the order            : the related order
 *   - the transaction_id   : the transaction token from the bank
 *   - the state            : the current state of the transaction, only OK or KO
 *   - the status           : the current status of the transaction
 *   - the error code       : the current error code of the transaction from the payment handler
 */
interface TransactionInterface
{
    public const STATE_OK = 1;
    public const STATE_KO = 2;

    public const STATUS_ORDER_UNKNOWN = -1; // the order is unknown
    public const STATUS_OPEN = 0;  // created but not validated
    public const STATUS_PENDING = 1;  // the bank send a 'pending-like' status, so the payment is not validated, but the user payed
    public const STATUS_VALIDATED = 2;  // the bank confirm the payment
    public const STATUS_CANCELLED = 3;  // the user cancelled the payment
    public const STATUS_UNKNOWN = 4;  // the bank sent a unknown code ...
    public const STATUS_ERROR_VALIDATION = 9;  // something wrong happen when the bank validate the postback
    public const STATUS_WRONG_CALLBACK = 10; // something wrong is sent from the bank. hack or the bank change something ...
    public const STATUS_WRONG_REQUEST = 11; // the callback request is not valid
    public const STATUS_ORDER_NOT_OPEN = 12; // the order is not open (so a previous transaction already alter the order)

    /**
     * @param \Sonata\Component\Order\OrderInterface $order
     */
    public function setOrder(OrderInterface $order);

    /**
     * @return \Sonata\Component\Order\OrderInterface
     */
    public function getOrder();

    /**
     * @param int $state
     */
    public function setState($state);

    /**
     * @return int
     */
    public function getState();

    /**
     * @param int $transactionId
     */
    public function setTransactionId($transactionId);

    /**
     * @return int
     */
    public function getTransactionId();

    /**
     * @return bool
     */
    public function isValid();

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters);

    /**
     * @return array
     */
    public function getParameters();

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($name, $default = null);

    /**
     * @param int $statusCode
     */
    public function setStatusCode($statusCode);

    /**
     * return integer.
     */
    public function getStatusCode();

    /**
     * return status list.
     *
     * @return array
     */
    public static function getStatusList();

    /**
     * @param \DateTime|null $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param string $paymentCode
     */
    public function setPaymentCode($paymentCode);

    /**
     * @return string
     */
    public function getPaymentCode();

    /**
     * @return string
     */
    public function getInformation();

    /**
     * @param string $message
     */
    public function setInformation($message);

    /**
     * @param string $message
     */
    public function addInformation($message);

    /**
     * @return string
     */
    public function getStatusName();
}
