<?php

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

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class CurrencyPriceCalculatorTest extends TestCase
{
    public function testGetPrice()
    {
        $currencyPriceCalculator = new CurrencyPriceCalculator();

        $product = $this->createMock('Sonata\Component\Product\ProductInterface');

        $currency = new Currency();
        $currency->setLabel('EUR');

        $product->expects($this->once())->method('getPrice')->will($this->returnValue(42.0));

        $this->assertEquals(42.0, $currencyPriceCalculator->getPrice($product, $currency));
    }
}
