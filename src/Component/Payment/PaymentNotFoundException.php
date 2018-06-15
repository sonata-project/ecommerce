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

use Exception;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class PaymentNotFoundException extends \InvalidArgumentException
{
    /**
     * @param string    $bankCode
     * @param int       $code
     * @param Exception $previous
     */
    public function __construct($bankCode, $code = 0, Exception $previous = null)
    {
        $message = sprintf("Payment method with code '%s' was not found", $bankCode);
        parent::__construct($message, $code, $previous);
    }
}
