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
        foreach ($basketElements as $basketElement) {
            if (!$basketElement instanceof \Sonata\Component\Basket\BasketElementInterface) {
                continue;
            }

            $basketElement->setBasket($this);
            $this->addBasketElement($basketElement);
        }
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
