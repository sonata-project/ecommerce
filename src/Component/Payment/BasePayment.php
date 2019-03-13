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

use Psr\Log\LoggerInterface;
use Sonata\Component\Order\OrderInterface;

abstract class BasePayment implements PaymentInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var array
     */
    protected $transformers;

    /**
     * @var \Psr\Log\LoggerInterface;
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $isDebug;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @param \Sonata\Component\Order\OrderInterface $order
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function generateUrlCheck(OrderInterface $order)
    {
        if (!$order->getCreatedAt() instanceof \DateTime) {
            throw new \RuntimeException(sprintf('The order must have a creation date - id:%s, reference:%s ', $order->getId(), $order->getReference()));
        }

        return sha1(
            $order->getReference().
            $order->getCreatedAt()->format('m/d/Y:G:i:s').
            $order->getId().
            $this->getOption('shop_secret_key')
        );
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code): void
    {
        $this->code = $code;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function setOptions(array $options): void
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
     * @param $name
     *
     * @return bool
     */
    public function hasOption($name)
    {
        return \array_key_exists($name, $this->options);
    }

    public function encodeString($value)
    {
        return $value;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    public function addTransformer($id, $transformer): void
    {
        $this->transformers[$id] = $transformer;
    }

    public function getTransformer($name)
    {
        return isset($this->transformers[$name]) ? $this->transformers[$name] : false;
    }

    public function callback(TransactionInterface $transaction)
    {
        // check if the order exists
        if (!$transaction->getOrder()) {
            $transaction->setStatusCode(TransactionInterface::STATUS_ORDER_UNKNOWN);
            $transaction->setState(TransactionInterface::STATE_KO);
            $transaction->setInformation('The order does not exist');

            return $this->handleError($transaction);
        }

        // check if the request is valid
        if (!$this->isRequestValid($transaction)) {
            $transaction->setStatusCode(TransactionInterface::STATUS_WRONG_REQUEST);
            $transaction->setState(TransactionInterface::STATE_KO);
            $transaction->setInformation('The request is not valid');

            return $this->handleError($transaction);
        }

        // check if the callback is valid
        if (!$this->isCallbackValid($transaction)) {
            $transaction->setStatusCode(TransactionInterface::STATUS_WRONG_CALLBACK);
            $transaction->setState(TransactionInterface::STATE_KO);
            $transaction->setInformation('The callback reference is not valid');

            return $this->handleError($transaction);
        }

        // apply the transaction id
        $this->applyTransactionId($transaction);

        // if the order is not open, then something already happen ... (duplicate callback)
        if (!$transaction->getOrder()->isOpen()) {
            $transaction->setState(TransactionInterface::STATE_OK); // the transaction is valid, but not the order state
            $transaction->setStatusCode(TransactionInterface::STATUS_ORDER_NOT_OPEN);
            $transaction->setInformation('The order is not open, then something already happen ... (duplicate callback)');

            return $this->handleError($transaction);
        }

        // send the confirmation request to the bank
        if (!($response = $this->sendConfirmationReceipt($transaction))) {
            $transaction->setInformation('Fail to send the confirmation receipt');

            $response = $this->handleError($transaction);
        }

        return $response;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param TransactionInterface $transaction
     */
    public function report(TransactionInterface $transaction): void
    {
        if (!$this->logger) {
            return;
        }

        if (TransactionInterface::STATE_KO === $transaction->getState()) {
            $method = 'crit';
        } else {
            $method = 'info';
        }

        foreach (explode("\n", (string) $transaction->getInformation()) as $message) {
            \call_user_func([$this->logger, $method], $message);
        }
    }
}
