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

namespace Sonata\Component\Delivery;

use Exception;
use Sonata\Component\Customer\AddressInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class UndeliverableCountryException extends \RuntimeException
{
    /**
     * @var AddressInterface
     */
    private $address;

    /**
     * @param AddressInterface $address
     * @param int              $code
     * @param Exception        $previous
     */
    public function __construct(AddressInterface $address, $code = 0, Exception $previous = null)
    {
        $this->address = $address;

        $message = sprintf("Some elements in your basket cannot be delivered in country '%s'.", $address->getCountryCode());
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return AddressInterface
     */
    public function getAddress()
    {
        return $this->address;
    }
}
