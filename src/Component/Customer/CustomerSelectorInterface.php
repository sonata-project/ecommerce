<?php

namespace Sonata\Component\Customer;

interface CustomerSelectorInterface
{
    /**
     * Get the customer.
     *
     * @return \Sonata\Component\Customer\CustomerInterface
     */
    public function get();
}
