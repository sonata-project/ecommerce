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

use Symfony\Component\HttpFoundation\Response;
use Sonata\Component\Payment\PaymentInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Payment\TransactionInterface;
use Symfony\Component\Routing\RouterInterface;
use Buzz\Browser;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class CheckPayment extends BasePayment
{
    protected $router;

    protected $browser;

    /**
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param \Buzz\Browser $browser
     */
    public function __construct(RouterInterface $router, LoggerInterface $logger, Browser $browser)
    {
        $this->router  = $router;
        $this->logger  = $logger;
        $this->browser = $browser;
    }

    /**
     * @return int
     */
    public function getTransactionId()
    {
        return 1;
    }

    /**
     * @param \Sonata\Component\Basket\BasketInterface $basket
     * @param \Sonata\Component\Product\ProductInterface $product
     * @return bool
     */
    public function isAddableProduct(BasketInterface $basket, ProductInterface $product)
    {
        return true;
    }

    /**
     * @param \Sonata\Component\Basket\BasketInterface $basket
     * @return bool
     */
    public function isBasketValid(BasketInterface $basket)
    {
        return true;
    }

    /**
     * @param TransactionInterface $transaction
     * @return bool
     */
    public function isRequestValid(TransactionInterface $transaction)
    {
        return true;
    }

    /**
     * Method called when an error occurs
     *
     * @param \Sonata\Component\Payment\TransactionInterface $transaction
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleError(TransactionInterface $transaction)
    {
        $transaction->getOrder()->setPaymentStatus($transaction->getStatusCode());

        $this->report($transaction);

        return new Response('ko', 200, array(
            'Content-Type' => 'text/plain',
        ));
    }

    /**
     * Send post back confirmation to the bank when the bank callback the site
     *
     * @param \Sonata\Component\Payment\TransactionInterface $transaction
     * @return \Symfony\Component\HttpFoundation\Response, false otherwise
     */
    public function sendConfirmationReceipt(TransactionInterface $transaction)
    {
        $order = $transaction->getOrder();
        if (!$order) {
            $transaction->setState(TransactionInterface::STATE_KO);
            $transaction->setStatusCode(TransactionInterface::STATUS_ORDER_UNKNOWN);
            $transaction->addInformation('The order does not exist');

            return false;
        }

        $transaction->setStatusCode(TransactionInterface::STATUS_VALIDATED);
        $transaction->setState(TransactionInterface::STATE_OK);

        $order->setStatus(OrderInterface::STATUS_PENDING);
        $order->setPaymentStatus(TransactionInterface::STATUS_PENDING);
        $order->setValidatedAt($transaction->getCreatedAt());

        return new Response('ok', 200, array(
            'Content-Type' => 'text/plain',
        ));

    }

    /**
     * @param TransactionInterface $transaction
     * @return bool
     */
    public function isCallbackValid(TransactionInterface $transaction)
    {
        if (!$transaction->getOrder()) {
            return false;
        }

        if ($transaction->get('check') == $this->generateUrlCheck($transaction->getOrder())) {
            return true;
        }

        $transaction->setState(TransactionInterface::STATE_KO);
        $transaction->setStatusCode(TransactionInterface::STATUS_WRONG_CALLBACK);
        $transaction->addInformation('The callback is not valid');

        return false;
    }

    /**
     * @param \Sonata\Component\Order\OrderInterface $order
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function callbank(OrderInterface $order)
    {
        $params = array(
            'bank'       => $this->getCode(),
            'reference'  => $order->getReference(),
            'check'      => $this->generateUrlCheck($order)
        );

        // call the callback handler ...
        $url = $this->router->generate($this->getOption('url_callback'), $params, true);

        $response = $this->browser->get($url);

        if ($response->getContent() == 'ok') {
            $routeName = 'url_return_ok';
        } else {
            $routeName = 'url_return_ko';

            $this->logger->crit(sprintf('The CheckPayment received a ko result : %s', $response->getContent()));
        }


        // redirect the user to the correct page
        $response = new Response('', 302, array(
            'Location' => $this->router->generate($this->getOption($routeName), $params, true),
        ));
        $response->setPrivate();

        return $response;
    }

    /**
     * @param TransactionInterface $transaction
     * @return string
     */
    public function getOrderReference(TransactionInterface $transaction)
    {
        return $transaction->get('reference');
    }

    /**
     * @param TransactionInterface $transaction
     * @return void
     */
    public function applyTransactionId(TransactionInterface $transaction)
    {
        $transaction->setTransactionId('n/a');
    }
}