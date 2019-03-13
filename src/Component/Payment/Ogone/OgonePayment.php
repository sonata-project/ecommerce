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

namespace Sonata\Component\Payment\Ogone;

use Psr\Log\LoggerInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Payment\BasePayment;
use Sonata\Component\Payment\TransactionInterface;
use Sonata\Component\Product\ProductInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Implements payment through Ogone's service.
 *
 * @author hbriand <hugo.briand@fullsix.net>
 */
class OgonePayment extends BasePayment
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @param RouterInterface $router
     * @param LoggerInterface $logger
     * @param EngineInterface $templating
     * @param bool            $debug
     */
    public function __construct(RouterInterface $router, LoggerInterface $logger, EngineInterface $templating, $debug)
    {
        $this->templating = $templating;
        $this->router = $router;
        $this->debug = $debug;

        $this->setLogger($logger);
    }

    public function isAddableProduct(BasketInterface $basket, ProductInterface $product)
    {
        return true;
    }

    public function isBasketValid(BasketInterface $basket)
    {
        return true;
    }

    public function isRequestValid(TransactionInterface $transaction)
    {
        $params = [
                'orderID' => $transaction->get('orderID'),
                'currency' => $transaction->get('currency'),
                'amount' => $transaction->get('amount'),
                'PM' => $transaction->get('PM'),
                'ACCEPTANCE' => $transaction->get('ACCEPTANCE'),
                'STATUS' => $transaction->get('STATUS'),
                'CARDNO' => $transaction->get('CARDNO'),
                'ED' => $transaction->get('ED'),
                'CN' => $transaction->get('CN'),
                'TRXDATE' => $transaction->get('TRXDATE'),
                'PAYID' => $transaction->get('PAYID'),
                'NCERROR' => $transaction->get('NCERROR'),
                'BRAND' => $transaction->get('BRAND'),
                'IP' => $transaction->get('IP'),
        ];

        $sha1 = $this->getShaSign($params, true);

        return $sha1 === $transaction->get('SHASIGN')
            && $this->compareOrderToParams($transaction->getOrder(), $params)
            && $transaction->get('check') === $this->generateUrlCheck($transaction->getOrder());
    }

    public function handleError(TransactionInterface $transaction)
    {
        if ($transaction->getOrder()->isOpen()) {
            $transaction->getOrder()->setPaymentStatus($transaction->getStatusCode());
        }

        $this->report($transaction);

        return new Response('ko', 200, [
            'Content-Type' => 'text/plain',
        ]);
    }

    public function sendConfirmationReceipt(TransactionInterface $transaction)
    {
        $transaction->setState(TransactionInterface::STATE_OK);
        $transaction->setStatusCode(TransactionInterface::STATUS_VALIDATED);

        $transaction->getOrder()->setValidatedAt(new \DateTime());
        $transaction->getOrder()->setStatus(OrderInterface::STATUS_VALIDATED);
        $transaction->getOrder()->setPaymentStatus(TransactionInterface::STATUS_VALIDATED);

        return new RedirectResponse(
            $this->router->generate($this->getOption('url_return_ok'), $transaction->getParameters())
        );
    }

    public function isCallbackValid(TransactionInterface $transaction)
    {
        if (!$transaction->getOrder()) {
            return false;
        }

        if ($transaction->get('check') === $this->generateUrlCheck($transaction->getOrder())) {
            return true;
        }

        $transaction->setState(TransactionInterface::STATE_KO);
        $transaction->setStatusCode(TransactionInterface::STATUS_WRONG_CALLBACK);
        $transaction->addInformation('The callback is not valid');

        return false;
    }

    public function sendbank(OrderInterface $order)
    {
        return $this->templating->renderResponse($this->getOption('template'), [
            'form_url' => $this->getOption('form_url'),
            'shasign' => $this->getShaSign($this->getFormParameters($order)),
            'fields' => $this->getFormParameters($order),
            'debug' => $this->debug,
        ]);
    }

    public function encodeString($string)
    {
        return $string;
    }

    public function getOrderReference(TransactionInterface $transaction)
    {
        return $transaction->get('reference');
    }

    /**
     * @param TransactionInterface $transaction
     */
    public function applyTransactionId(TransactionInterface $transaction): void
    {
        $transaction->setTransactionId('n/a');
    }

    /**
     * Tells if $order matches $params.
     *
     * @param OrderInterface $order
     * @param array          $params
     *
     * @return bool
     */
    protected function compareOrderToParams(OrderInterface $order, array $params)
    {
        return $order->getReference() === $params['orderID']
            && $order->getCurrency() === $params['currency']
            && (float) ($order->getTotalInc()) === (float) ($params['amount']);
    }

    /**
     * Signs the payment transaction.
     *
     * @param array $params
     * @param bool  $out    Should we use the out sha key?
     *
     * @return string
     */
    protected function getShaSign(array $params, $out = false)
    {
        uksort($params, 'strcasecmp');

        $shaKey = $this->getOption('sha'.($out ? '-out' : '').'_key');

        $shasignStr = '';
        foreach ($params as $key => $param) {
            if (null !== $param && '' !== $param) {
                $shasignStr .= strtoupper($key).'='.$param.$shaKey;
            }
        }

        return strtoupper(sha1($shasignStr));
    }

    /**
     * Returns form parameters for sendbank.
     *
     * @param OrderInterface $order
     *
     * @return array
     */
    protected function getFormParameters(OrderInterface $order)
    {
        return [
            'PSPID' => $this->getOption('pspid'),
            'orderId' => $order->getReference(),
            'amount' => $order->getTotalInc() * 100,
            'currency' => $order->getCurrency()->getLabel(),
            'language' => $order->getLocale(),
            'CN' => $order->getBillingName(),
            'EMAIL' => $order->getBillingEmail(),
            'ownerZIP' => $order->getBillingPostcode(),
            'owneraddress' => $this->getAddress($order),
            'ownercty' => $order->getBillingCity(),
            'ownertelno' => $order->getBillingPhone() ?: $order->getBillingMobile(),

            'homeurl' => $this->getOption('home_url'),
            'catalogurl' => $this->getOption('catalog_url'),

            'COMPLUS' => '',
            'PARAMPLUS' => 'bank='.$this->getCode(),
            'PARAMVAR' => '',

            'PM' => 'CreditCard',
            'WIN3DS' => 'MAINW',
            'PMLIST' => 'VISA;MasterCard;CB',
            'PMListType' => 2,

            'accepturl' => $this->generateAbsoluteUrlFromOption('url_callback', $order),
            'declineurl' => $this->generateAbsoluteUrlFromOption('url_callback', $order),
            'exceptionurl' => $this->generateAbsoluteUrlFromOption('url_return_ko', $order),
            'cancelurl' => $this->generateAbsoluteUrlFromOption('url_return_ko', $order),

            'operation' => $this->getOperation(),
        ];
    }

    /**
     * Generates absolute URL for route specified in $optionKey and $order.
     *
     * @param string         $optionKey
     * @param OrderInterface $order
     *
     * @return string
     */
    protected function generateAbsoluteUrlFromOption($optionKey, OrderInterface $order)
    {
        return $this->router->generate(
            $this->getOption($optionKey),
            [
                'bank' => $this->getCode(),
                'reference' => $order->getReference(),
                'check' => $this->generateUrlCheck($order),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * Gets formatted address lines from $order.
     *
     * @param OrderInterface $order
     *
     * @return string
     */
    protected function getAddress(OrderInterface $order)
    {
        $ret = $order->getBillingAddress1();
        if (null !== $order->getBillingAddress2()) {
            $ret .= ' '.$order->getBillingAddress2();
        }
        if (null !== $order->getBillingAddress3()) {
            $ret .= ' '.$order->getBillingAddress3();
        }

        return $ret;
    }

    /**
     * Gets operation from options.
     *
     * @return string
     */
    protected function getOperation()
    {
        if ($this->getOption('differed', false)) {
            return 'RES';
        }

        return 'SAL';
    }
}
