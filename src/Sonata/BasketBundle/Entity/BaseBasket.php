<?php

namespace Sonata\BasketBundle\Entity;

use Sonata\Component\Basket\Basket;
use Doctrine\Common\Collections\ArrayCollection;

abstract class BaseBasket extends Basket
{
    public function __construct()
    {
        $this->reset(true);
    }

    /**
     * {@inheritdoc}
     */
    public function setBasketElements($basketElements)
    {
        foreach($basketElements as $basketElement) {
            $basketElement->setBasket($this);
        }

        $this->basketElements = $basketElements;
    }

    /**
     * {@inheritdoc}
     */
    public function reset($full = true)
    {
        parent::reset($full);

        if ($full) {
            $this->basketElements = new ArrayCollection;
        }
    }
}