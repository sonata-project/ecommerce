<?php

namespace Sonata\Component\Basket;

use Sonata\Component\Customer\CustomerInterface;

interface BasketFactoryInterface
{
    /**
     * Load the basket
     *
     * @param \Sonata\Component\Customer\CustomerInterface
     * @return \Sonata\Component\Basket\BasketInterface
     */
    function load(CustomerInterface $customer);

    /**
     * Save the basket
     *
     * @param \Sonata\Component\Basket\BasketInterface
     * @return void
     */
    function save(BasketInterface $basket);
}
