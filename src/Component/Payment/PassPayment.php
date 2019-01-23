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

use Buzz\Browser;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Product\ProductInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class PassPayment extends BasePayment
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var Browser
     */
    protected $browser;

    /**
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param \Buzz\Browser                              $browser
     */
    public function __construct(RouterInterface $router, Browser $browser = null)
    {
        $this->router = $router;
        $this->browser = $browser;
    }

    /**
     * {@inheritdoc}
     */
    public function encodeString($value)
    {
        return $value;
    }

    /**
     * @return int
     */
    public function getTransactionId()
    {
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function isAddableProduct(BasketInterface $basket, ProductInterface $product)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isBasketValid(BasketInterface $basket)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isRequestValid(TransactionInterface $transaction)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function handleError(TransactionInterface $transaction)
    {
        return new Response('ko', 200, [
            'Content-Type' => 'text/plain',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function sendConfirmationReceipt(TransactionInterface $transaction)
    {
        $order = $transaction->getOrder();
        if (!$order) {
            $transaction->setState(TransactionInterface::STATE_KO);
            $transaction->setStatusCode(TransactionInterface::STATUS_ORDER_UNKNOWN);

            return false;
        }

        $transaction->setStatusCode(TransactionInterface::STATUS_VALIDATED);
        $transaction->setState(TransactionInterface::STATE_OK);

        $order->setStatus(OrderInterface::STATUS_VALIDATED);
        $order->setPaymentStatus(TransactionInterface::STATUS_VALIDATED);
        $order->setValidatedAt($transaction->getCreatedAt());

        return new Response('ok', 200, [
            'Content-Type' => 'text/plain',
        ]);
    }

    /**
     * {@inheritdoc}
     */
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

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function sendbank(OrderInterface $order)
    {
        $params = [
            'bank' => $this->getCode(),
            'reference' => $order->getReference(),
            'check' => $this->generateUrlCheck($order),
        ];

        // call the callback handler ...
        $url = $this->router->generate($this->getOption('url_callback'), $params, UrlGeneratorInterface::ABSOLUTE_URL);

        $response = $this->browser->get($url);

        $routeName = 'ok' === $response->getContent() ? 'url_return_ok' : 'url_return_ko';

        // redirect the user to the correct page
        $response = new Response('', 302, [
            'Location' => $this->router->generate($this->getOption($routeName), $params, UrlGeneratorInterface::ABSOLUTE_URL),
            'Content-Type' => 'text/plain',
        ]);

        $response->setPrivate();

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderReference(TransactionInterface $transaction)
    {
        return $transaction->get('reference');
    }

    /**
     * {@inheritdoc}
     */
    public function applyTransactionId(TransactionInterface $transaction)
    {
        $transaction->setTransactionId('n/a');
    }
}
