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

namespace Sonata\Component\Event;

use Sonata\Component\Basket\BasketInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class BasketTransformEvent extends Event
{
    /**
     * @var BasketInterface
     */
    protected $basket;

    /**
     * @param BasketInterface $basket
     */
    public function __construct(BasketInterface $basket)
    {
        $this->basket = $basket;
    }

    /**
     * @return \Sonata\Component\Basket\BasketInterface
     */
    public function getBasket()
    {
        return $this->basket;
    }
}
