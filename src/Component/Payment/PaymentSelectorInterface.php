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

use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Customer\AddressInterface;

interface PaymentSelectorInterface
{
    /**
     * Returns the available Payment methods for given $basket and $deliveryAddress.
     *
     * @param BasketInterface  $basket
     * @param AddressInterface $deliveryAddress
     *
     * @return array
     */
    public function getAvailableMethods(BasketInterface $basket = null, AddressInterface $deliveryAddress = null);

    /**
     * Returns the Payment method for given $bank.
     *
     * @param $bank The payment method code
     *
     * @throws PaymentNotFoundException
     *
     * @return PaymentInterface
     */
    public function getPayment($bank);
}
