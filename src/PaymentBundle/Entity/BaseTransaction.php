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

namespace Sonata\PaymentBundle\Entity;

use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Payment\TransactionInterface;

/**
 * The Transaction class represents a callback request from the bank.
 *
 * The object contains :
 *   - the order            : the related order
 *   - the transactionId    : the transaction token from the bank
 *   - the state            : the current state of the transaction, only OK or KO
 *   - the status           : the current status of the transaction
 *   - the error code       : the current error code of the transaction from the payment handler
 */
class BaseTransaction implements TransactionInterface
{
    /**
     * @var OrderInterface
     */
    protected $order;

    /**
     * @var int
     */
    protected $transactionId;

    /**
     * @var int
     */
    protected $state;

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var string
     */
    protected $statusCode;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var string
     */
    protected $paymentCode;

    /**
     * @var string
     */
    protected $information;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->transactionId = 'n/a';
        $this->information = 'Transaction created';
        $this->setStatusCode(self::STATUS_OPEN);
    }

    /**
     * @param \Sonata\Component\Order\OrderInterface $order
     */
    public function setOrder(OrderInterface $order): void
    {
        $this->order = $order;

        $this->addInformation(sprintf('The transaction is linked to the Order : id = `%s` / Reference = `%s`', $order->getId(), $order->getReference()));
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
     */
    public function setState($state): void
    {
        $this->state = $state;

        if (self::STATE_OK === $state) {
            $this->addInformation('The transaction state is `OK`');
        } elseif (self::STATE_OK === $state) {
            $this->addInformation('The transaction state is `KO`');
        } else {
            $this->addInformation('The transaction state is `UNKNOWN`');
        }
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
     */
    public function setTransactionId($transactionId): void
    {
        $this->transactionId = $transactionId;
        $this->addInformation(sprintf('The transactionId is `%s`', $transactionId));
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
        return TransactionInterface::STATE_OK === $this->state;
    }

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $this->cleanupEncoding($parameters);
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param      $name
     * @param null $default
     *
     * @return mixed|null
     */
    public function get($name, $default = null)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : $default;
    }

    /**
     * @param $statusCode
     */
    public function setStatusCode($statusCode): void
    {
        $this->statusCode = $statusCode;

        $this->addInformation(sprintf('Update status code to `%s` (%s)', $statusCode, $this->getStatusName()));
    }

    /**
     * @return string
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        $list = self::getStatusList();

        if (!isset($list[$this->getStatusCode()])) {
            return 'n/a';
        }

        return $list[$this->getStatusCode()];
    }

    /**
     * return status list.
     *
     * @return array
     */
    public static function getStatusList()
    {
        return [
            TransactionInterface::STATUS_ORDER_UNKNOWN => 'order_unknown',
            TransactionInterface::STATUS_OPEN => 'open',
            TransactionInterface::STATUS_PENDING => 'pending',
            TransactionInterface::STATUS_VALIDATED => 'validated',
            TransactionInterface::STATUS_CANCELLED => 'cancelled',
            TransactionInterface::STATUS_UNKNOWN => 'status_unknown',
            TransactionInterface::STATUS_ERROR_VALIDATION => 'error_validation',
            TransactionInterface::STATUS_WRONG_CALLBACK => 'wrong_callback',
            TransactionInterface::STATUS_WRONG_REQUEST => 'wrong_request',
            TransactionInterface::STATUS_ORDER_NOT_OPEN => 'order_to_open',
        ];
    }

    /**
     * @return array
     */
    public static function getValidationStatusList()
    {
        return array_keys(self::getStatusList());
    }

    /**
     * @param \DateTime|null $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null): void
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
     */
    public function setPaymentCode($paymentCode): void
    {
        $this->paymentCode = $paymentCode;
    }

    /**
     * @return string
     */
    public function getPaymentCode()
    {
        return $this->paymentCode;
    }

    /**
     * @param $information
     */
    public function addInformation($information): void
    {
        $this->information .= "\n".$information;
    }

    /**
     * @param $information
     */
    public function setInformation($information): void
    {
        $this->information = $information;
    }

    /**
     * @return string
     */
    public function getInformation()
    {
        return $this->information;
    }

    /**
     * Cleans up $toDecode keys & values.
     *
     * @param array $toDecode
     *
     * @return array
     */
    protected function cleanupEncoding(array $toDecode)
    {
        $decodedParams = [];
        foreach ($toDecode as $key => $value) {
            $decodedValue = \is_array($value) ? $this->cleanupEncoding($value) : (mb_check_encoding($value, 'UTF-8') ? $value : utf8_encode($value));
            $decodedParams[mb_check_encoding($key, 'UTF-8') ? $key : utf8_encode($key)] = $decodedValue;
        }

        return $decodedParams;
    }
}
