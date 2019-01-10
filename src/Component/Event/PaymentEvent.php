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

namespace Sonata\Component\Event;

use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Payment\TransactionInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class PaymentEvent extends Event
{
    /**
     * @var OrderInterface
     */
    protected $order;

    /**
     * @var TransactionInterface
     */
    protected $transaction;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @param OrderInterface       $order
     * @param TransactionInterface $transaction
     * @param Response             $response
     */
    public function __construct(OrderInterface $order, TransactionInterface $transaction = null, Response $response = null)
    {
        $this->order = $order;
        $this->transaction = $transaction;
        $this->response = $response;
    }

    /**
     * @return \Sonata\Component\Order\OrderInterface
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return \Sonata\Component\Payment\TransactionInterface
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
