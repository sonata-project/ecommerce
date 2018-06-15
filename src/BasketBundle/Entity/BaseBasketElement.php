<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\BasketBundle\Entity;

use Sonata\Component\Basket\BasketElement;
use Sonata\Component\Basket\BasketInterface;

abstract class BaseBasketElement extends BasketElement
{
    /**
     * @var \Sonata\Component\Basket\BasketInterface
     */
    protected $basket;

    /**
     * Get basket.
     *
     * @return \Sonata\Component\Basket\BasketInterface $basket
     */
    public function getBasket()
    {
        return $this->basket;
    }

    /**
     * Set basket.
     *
     * @param \Sonata\Component\Basket\BasketInterface $basket
     */
    public function setBasket(BasketInterface $basket): void
    {
        $this->basket = $basket;
    }
}
