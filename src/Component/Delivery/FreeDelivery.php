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

/**
 * A free delivery method, used this only for testing.
 */
class FreeDelivery extends BaseServiceDelivery
{
    /**
     * @var bool
     */
    protected $isAddressRequired;

    /**
     * @param bool $isAddressRequired
     */
    public function __construct($isAddressRequired)
    {
        $this->isAddressRequired = $isAddressRequired;
    }

    public function getVatRate()
    {
        return 0;
    }

    public function getPrice()
    {
        return 0;
    }

    public function isAddressRequired()
    {
        return $this->isAddressRequired;
    }

    public function getName()
    {
        return 'free_address_required';
    }
}
