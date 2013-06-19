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

use Sonata\Component\Payment\PaymentInterface;

/**
 * The pool stored a group of available payment method
 *
 */
class Pool
{
    /**
     * @var array
     */
    protected $methods = array();

    /**
     * add a payment method into the pool
     *
     * @param  \Sonata\Component\Payment\PaymentInterface $instance
     * @return void
     */
    public function addMethod(PaymentInterface $instance)
    {
        $this->methods[$instance->getCode()] = $instance;
    }

    /**
     *
     * @return array of payment methods
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * return a PaymentInterface Object
     *
     * @param  string                                    $code
     * @return Sonata\Component\Payment\PaymentInterface
     */
    public function getMethod($code)
    {
        return isset($this->methods[$code]) ? $this->methods[$code] : null;
    }
}
