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

namespace Sonata\Component\Tests\Currency;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Currency\Currency;
use Sonata\Component\Currency\CurrencyPriceCalculator;
use Sonata\Component\Product\ProductInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class CurrencyPriceCalculatorTest extends TestCase
{
    public function testGetPrice(): void
    {
        $currencyPriceCalculator = new CurrencyPriceCalculator();

        $product = $this->createMock(ProductInterface::class);

        $currency = new Currency();
        $currency->setLabel('EUR');

        $product->expects($this->once())->method('getPrice')->will($this->returnValue(42.0));

        $this->assertSame(42.0, $currencyPriceCalculator->getPrice($product, $currency));
    }
}
