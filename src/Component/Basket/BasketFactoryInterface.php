<?php

namespace Sonata\Component\Basket;

use Sonata\Component\Customer\CustomerInterface;

interface BasketFactoryInterface
{
    /**
     * Load the basket.
     *
     * @param \Sonata\Component\Customer\CustomerInterface
     *
     * @return \Sonata\Component\Basket\BasketInterface
     */
    public function load(CustomerInterface $customer);

    /**
     * Save the basket.
     *
     * @param \Sonata\Component\Basket\BasketInterface
     */
    public function save(BasketInterface $basket);

    /**
     * Resets the basket.
     *
     * @param BasketInterface $basket
     * @param bool            $full
     */
    public function reset(BasketInterface $basket, $full = true);
}
