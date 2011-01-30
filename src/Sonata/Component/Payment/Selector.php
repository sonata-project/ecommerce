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
    protected $paymentPool;

    protected $productPool;

    protected $logger;


    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function setProductPool($productPool)
    {
        $this->productPool = $productPool;
    }

    public function getProductPool()
    {
        return $this->productPool;
    }

    public function getAvailableMethods($basket, $paymentAddress)
    {

        if (!$paymentAddress) {

            return false;
        }

        return $this->getPaymentPool()->getMethods();
    }

    public function setPaymentPool($paymentPool)
    {
        $this->paymentPool = $paymentPool;
    }

    public function getPaymentPool()
    {
        return $this->paymentPool;
    }
}