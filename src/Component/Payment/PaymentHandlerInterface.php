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

use Doctrine\ORM\EntityNotFoundException;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Order\OrderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface PaymentHandlerInterface
{
    /**
     * Processes the request to generate the transaction related to the error and returns associated order.
     *
     * @param Request         $request
     * @param BasketInterface $basket
     *
     * @throws EntityNotFoundException
     * @throws InvalidTransactionException
     *
     * @return OrderInterface
     */
    public function handleError(Request $request, BasketInterface $basket);

    /**
     * Returns the order for given confirmation request and checks the validity.
     *
     * @param Request $request
     *
     * @throws EntityNotFoundException
     * @throws InvalidTransactionException
     *
     * @return OrderInterface
     */
    public function handleConfirmation(Request $request);

    /**
     * Creates the order based on current basket & resets the basket.
     *
     * @param BasketInterface $basket
     *
     * @return OrderInterface
     */
    public function getSendbankOrder(BasketInterface $basket);

    /**
     * Returns the callback response of current payment mean once everything validated.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getPaymentCallbackResponse(Request $request);
}
