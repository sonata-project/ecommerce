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

use Doctrine\Common\Collections\ArrayCollection;
use Sonata\Component\Basket\Basket;
use Sonata\Component\Basket\BasketElementInterface;

abstract class BaseBasket extends Basket
{
    public function __construct()
    {
        $this->reset(true);
    }

    public function setBasketElements($basketElements): void
    {
        foreach ($basketElements as $basketElement) {
            if (!$basketElement instanceof BasketElementInterface) {
                continue;
            }

            $basketElement->setBasket($this);
            $this->addBasketElement($basketElement);
        }
    }

    public function reset($full = true): void
    {
        parent::reset($full);

        if ($full) {
            $this->basketElements = new ArrayCollection();
        }
    }
}
