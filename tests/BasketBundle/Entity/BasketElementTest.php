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

namespace Sonata\BasketBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Basket\BasketElement;

/**
 * @author Vincent Composieux <composieux@ekino.com>
 */
class BasketElementTest extends TestCase
{
    /**
     * Test quantity setter, if quantity is negative, must returns a quantity of 1.
     */
    public function testNegativeQuantity(): void
    {
        $basketElement = new BasketElement();

        $basketElement->setQuantity(50);
        $this->assertSame(50, $basketElement->getQuantity());

        $basketElement->setQuantity(-50);
        $this->assertSame(1, $basketElement->getQuantity());
    }
}
