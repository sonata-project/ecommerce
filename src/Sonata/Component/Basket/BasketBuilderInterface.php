<?php

namespace Sonata\Component\Basket;

interface BasketBuilderInterface
{
    /**
     * Build a basket
     *
     * @param \Sonata\Component\Basket\BasketInterface $basket
     */
    function build(BasketInterface $basket);
}
