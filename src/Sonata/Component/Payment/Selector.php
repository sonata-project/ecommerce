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

use Sonata\Component\Payment\Pool as PaymentPool;
use Sonata\Component\Product\Pool as ProductPool;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Customer\AddressInterface;

/**
 * The selector selects available payment methods depends on the provided basket
 *
 */
class Selector implements PaymentSelectorInterface
{
    protected $paymentPool;

    protected $productPool;

    protected $logger;

    public function __construct(PaymentPool $paymentPool, ProductPool $productPool, $logger = null)
    {
        $this->paymentPool = $paymentPool;
        $this->productPool = $productPool;
        $this->logger = $logger;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function getProductPool()
    {
        return $this->productPool;
    }

    public function getAvailableMethods(BasketInterface $basket = null, AddressInterface $paymentAddress = null)
    {
        if (!$paymentAddress) {
            return false;
        }

        return $this->getPaymentPool()->getMethods();
    }

    public function getPaymentPool()
    {
        return $this->paymentPool;
    }
}
