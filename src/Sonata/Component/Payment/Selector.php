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
use Symfony\Component\HttpKernel\Log\LoggerInterface;

/**
 * The selector selects available payment methods depends on the provided basket
 *
 */
class Selector implements PaymentSelectorInterface
{
    /**
     * @var PaymentPool
     */
    protected $paymentPool;

    /**
     * @var ProductPool
     */
    protected $productPool;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param PaymentPool     $paymentPool
     * @param ProductPool     $productPool
     * @param LoggerInterface $logger
     */
    public function __construct(PaymentPool $paymentPool, ProductPool $productPool, LoggerInterface $logger = null)
    {
        $this->paymentPool = $paymentPool;
        $this->productPool = $productPool;
        $this->logger = $logger;
    }

    /**
     * @param LoggerInterface $logger
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

    /**
     * @return \Sonata\Component\Product\Pool
     */
    public function getProductPool()
    {
        return $this->productPool;
    }

    /**
     * {@inheritDoc}
     */
    public function getAvailableMethods(BasketInterface $basket = null, AddressInterface $paymentAddress = null)
    {
        if (!$paymentAddress) {
            return false;
        }

        return $this->getPaymentPool()->getMethods();
    }

    /**
     * @return PaymentPool|null
     */
    public function getPaymentPool()
    {
        return $this->paymentPool;
    }
}
