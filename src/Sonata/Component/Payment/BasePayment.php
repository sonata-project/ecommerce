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

use Sonata\Component\Payment\PaymentInterface;
use Sonata\Component\Order\OrderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

abstract class BasePayment implements PaymentInterface
{
    protected $name;

    protected $code;

    protected $options;

    protected $transformers;

    /**
     * @var \Symfony\Component\HttpKernel\Log\LoggerInterface
     */
    protected $logger;

    protected $isDebug;

    protected $enabled;

    protected $description;

    /**
     * @param \Sonata\Component\Order\OrderInterface $order
     * @return string
     */
    public function generateUrlCheck(OrderInterface $order)
    {
        if (!$order->getCreatedAt() instanceof \DateTime) {
            throw new \RuntimeException(sprintf('The order must have a creation date - id:%s, reference:%s ', $order->getId(), $order->getReference()));
        }

        return sha1(
            $order->getReference().
            $order->getCreatedAt()->format("m/d/Y:G:i:s").
            $order->getId().
            $this->getOption('shop_secret_key')
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

    /**
     * @param $options
     * @return void
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $name
     * @param null $default
     * @return null
     */
    public function getOption($name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
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
     * @param \Symfony\Component\HttpKernel\Log\LoggerInterface $logger
     * @return void
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return \Symfony\Component\HttpKernel\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    public function addTransformer($id, $transformer)
    {
        $this->transformers[$id] = $transformer;
    }

    public function getTransformer($name)
    {
        return isset($this->transformers[$name]) ? $this->transformers[$name] : false;
    }

    /**
     * @param TransactionInterface $transaction
     * @return Response
     */
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
            $transaction->setInformation('The callbak reference is not valid');

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
     * @param boolean $enabled
     * @return void
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param TransactionInterface $transaction
     * @return mixed
     */
    public function report(TransactionInterface $transaction)
    {
        if (!$this->logger) {
            return;
        }

        if ($transaction->getState() == TransactionInterface::STATE_KO) {
            $method = 'crit';
        } else {
            $method = 'info';
        }

        foreach (explode("\n", $transaction->getInformation()) as $message) {
            call_user_func(array($this->logger, $method), $message);
        }
    }
}