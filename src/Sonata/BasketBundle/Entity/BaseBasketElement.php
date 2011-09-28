<?php

namespace Sonata\BasketBundle\Entity;

use Sonata\Component\Basket\BasketElement;
use Sonata\Component\Basket\BasketInterface;

abstract class BaseBasketElement extends BasketElement
{
    /**
     * @var \Sonata\Component\Basket\BasketInterface $basket
     */
    protected $basket;

    /**
     * Get basket
     *
     * @return \Sonata\Component\Basket\BasketInterface $basket
     */
    public function getBasket()
    {
        return $this->basket;
    }

    /**
     * Set basket
     *
     * @param \Sonata\Component\Basket\BasketInterface $basket
     */
    public function setBasket(BasketInterface $basket)
    {
        $this->basket = $basket;
    }

    /**
     * @param array $basketElements
     * @return void
     */
    public function setBasketElements($basketElements)
    {
        foreach($basketElements as $basketElement) {
            $basketElement->setBasket($this);
        }

        $this->basketElements = $basketElements;
    }
}