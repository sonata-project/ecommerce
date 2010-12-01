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

/**
 * The selector selects available payment methods depends on the provided basket
 *
 */
class Selector
{
    protected $payment_pool;

    protected $product_pool;

    protected $logger;


    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function setProductPool($product_pool)
    {
        $this->product_pool = $product_pool;
    }

    public function getProductPool()
    {
        return $this->product_pool;
    }

    public function getAvailableMethods($basket, $payment_address)
    {

        if (!$payment_address) {

            return false;
        }

        return $this->getPaymentPool()->getMethods();
    }

    public function setPaymentPool($payment_pool)
    {
        $this->payment_pool = $payment_pool;
    }

    public function getPaymentPool()
    {
        return $this->payment_pool;
    }
}