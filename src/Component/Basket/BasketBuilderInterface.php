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
