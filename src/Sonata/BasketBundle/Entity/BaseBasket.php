<?php

namespace Sonata\BasketBundle\Entity;

use Sonata\Component\Basket\Basket;
use Doctrine\Common\Collections\ArrayCollection;

abstract class BaseBasket extends Basket
{
    public function __construct()
    {
        $this->basketElements = new ArrayCollection;
    }
}
