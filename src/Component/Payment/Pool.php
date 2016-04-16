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

/**
 * The pool stored a group of available payment method.
 */
class Pool
{
    /**
     * @var array
     */
    protected $methods = array();

    /**
     * add a payment method into the pool.
     *
     * @param PaymentInterface $instance
     */
    public function addMethod(PaymentInterface $instance)
    {
        $this->methods[$instance->getCode()] = $instance;
    }

    /**
     * @return array of payment methods
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * return a PaymentInterface Object.
     *
     * @param string $code
     *
     * @return PaymentInterface
     */
    public function getMethod($code)
    {
        return isset($this->methods[$code]) ? $this->methods[$code] : null;
    }
}
