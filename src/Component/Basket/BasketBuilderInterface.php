<?php

namespace Sonata\Component\Basket;

interface BasketBuilderInterface
{
    /**
     * Build a basket.
     *
     * @param \Sonata\Component\Basket\BasketInterface $basket
     */
    public function build(BasketInterface $basket);
}
