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
 * A free delivery method, used this only for testing.
 */
class FreeDelivery extends BaseServiceDelivery
{
    /**
     * @var bool
     */
    protected $isAddressRequired;

    /**
     * Constructor.
     *
     * @param bool $isAddressRequired
     */
    public function __construct($isAddressRequired)
    {
        $this->isAddressRequired = $isAddressRequired;
    }

    /**
     * {@inheritdoc}
     */
    public function getVatRate()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isAddressRequired()
    {
        return $this->isAddressRequired;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Free delivery';
    }
}
