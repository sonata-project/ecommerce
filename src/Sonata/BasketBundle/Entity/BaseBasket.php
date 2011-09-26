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

    /**
     * Get pos
     *
     * @return string $pos
     */
//    public function getPos()
//    {
//        return $this->pos;
//    }
//
//    /**
//     * Set pos
//     *
//     * @param string $pos
//     */
//    public function setPos($pos)
//    {
//        $this->pos = $pos;
//    }
//
//    /**
//     * Get cptElement
//     *
//     * @return integer $cptElement
//     */
//    public function getCptElement()
//    {
//        return $this->cptElement;
//    }
//
//    /**
//     * Set cptElement
//     *
//     * @param integer $cptElement
//     */
//    public function setCptElement($cptElement)
//    {
//        $this->cptElement = $cptElement;
//    }
//
//    /**
//     * Set deliveryMethodCode
//     *
//     * @param string $deliveryMethodCode
//     */
//    public function setDeliveryMethodCode($deliveryMethodCode)
//    {
//        $this->deliveryMethodCode = $deliveryMethodCode;
//    }
}
