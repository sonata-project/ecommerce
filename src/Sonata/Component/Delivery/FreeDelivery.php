<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Delivery;

/**
 * A free delivery method, used this only for testing
 *
 */
class FreeDelivery extends BaseDelivery
{
    protected $isAddressRequired;

    /**
     * Constructor
     *
     * @param boolean $isAddressRequired
     */
    public function __construct($isAddressRequired)
    {
        $this->isAddressRequired = $isAddressRequired;
    }

    /**
     * Get vat
     *
     * @return float
     */
    public function getVat()
    {
        return 0;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return 0;
    }

    /**
     * Is address required?
     *
     * @return boolean
     */
    public function isAddressRequired()
    {
        return $this->isAddressRequired;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'Free delivery';
    }
}
