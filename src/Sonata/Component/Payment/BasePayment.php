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
 * A free delivery method, used this only for testing
 *
 */
abstract class BasePayment implements PaymentInterface
{
    protected
        $name,
        $code,
        $options;

    public function getCode() {

        return $this->code;
    }

    public function setCode($code) {

        $this->code = $code;
    }

    public function getName() {

        return $this->name;
    }

    public function setName($name) {

        $this->name = $name;
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function getOptions()
    {
        return $this->options;
    }

}