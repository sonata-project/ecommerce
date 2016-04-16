<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Payment;

use Sonata\Component\Basket\BasketInterface;
use Symfony\Component\HttpFoundation\Request;

interface PaymentHandlerInterface
{
    /**
     * Processes the request to generate the transaction related to the error and returns associated order.
     *
     * @param Request         $request
     * @param BasketInterface $basket
     *
     * @return \Sonata\Component\Order\OrderInterface
     *
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws InvalidTransactionException
     */
    public function handleError(Request $request, BasketInterface $basket);

    /**
     * Returns the order for given confirmation request and checks the validity.
     *
     * @param Request $request
     *
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws InvalidTransactionException
     *
     * @return \Sonata\Component\Order\OrderInterface
     */
    public function handleConfirmation(Request $request);

    /**
     * Creates the order based on current basket & resets the basket.
     *
     * @param BasketInterface $basket
     *
     * @return \Sonata\Component\Order\OrderInterface
     */
    public function getSendbankOrder(BasketInterface $basket);

    /**
     * Returns the callback response of current payment mean once everything validated.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getPaymentCallbackResponse(Request $request);
}
