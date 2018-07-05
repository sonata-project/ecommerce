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

namespace Sonata\Component\Payment\Debug;

use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Payment\PassPayment;
use Sonata\Component\Payment\TransactionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Implement debug payment.
 *
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
class DebugPayment extends PassPayment
{
    public function sendbank(OrderInterface $order)
    {
        return new RedirectResponse($this->router->generate('sonata_payment_debug', [
            'check' => $this->generateUrlCheck($order),
            'reference' => $order->getReference(),
        ]));
    }

    /**
     * Handle the callback.
     *
     * @param OrderInterface $order
     * @param string         $action
     *
     * @return Response
     */
    public function processCallback(OrderInterface $order, $action)
    {
        $params = [
            'bank' => $this->getCode(),
            'reference' => $order->getReference(),
            'check' => $this->generateUrlCheck($order),
            'action' => $action,
        ];

        $url = $this->router->generate($this->getOption('url_callback'), $params, UrlGeneratorInterface::ABSOLUTE_URL);

        $response = $this->browser->get($url);

        $routeName = 'ok' === $response->getContent() ? 'url_return_ok' : 'url_return_ko';

        $response = new RedirectResponse($this->router->generate($this->getOption($routeName), $params, true), 302, [
            'Content-Type' => 'text/plain',
        ]);
        $response->setPrivate();

        return $response;
    }

    public function sendConfirmationReceipt(TransactionInterface $transaction)
    {
        $parameters = $transaction->getParameters();

        if (!array_key_exists('action', $parameters)) {
            throw new \RuntimeException('"action" parameter is missing from Transaction.');
        }

        switch ($parameters['action']) {
            case 'accept':
                $transaction->setState(TransactionInterface::STATE_OK);
                $transaction->setStatusCode(TransactionInterface::STATUS_VALIDATED);

                $transaction->getOrder()->setValidatedAt(new \DateTime());
                $transaction->getOrder()->setStatus(OrderInterface::STATUS_VALIDATED);
                $transaction->getOrder()->setPaymentStatus(TransactionInterface::STATUS_VALIDATED);

                return new Response('ok', 200, [
                    'Content-Type' => 'text/plain',
                ]);

            case 'refuse':
                $transaction->setState(TransactionInterface::STATE_KO);
                $transaction->setStatusCode(TransactionInterface::STATUS_ERROR_VALIDATION);

                return false;

            default:
                $transaction->setState(TransactionInterface::STATE_KO);
                $transaction->setStatusCode(TransactionInterface::STATUS_ERROR_VALIDATION);

                return false;
        }
    }

    public function isRequestValid(TransactionInterface $transaction)
    {
        return $transaction->get('check') === $this->generateUrlCheck($transaction->getOrder());
    }
}
