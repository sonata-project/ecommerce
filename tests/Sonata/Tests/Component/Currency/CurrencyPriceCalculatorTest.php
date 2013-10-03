<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\Test\Component\Currency\Types;

use Sonata\Component\Currency\Currency;
use Sonata\Component\Currency\CurrencyPriceCalculator;
use Sonata\Component\Currency\UnavailableForCurrencyException;


/**
 * Class CurrencyPriceCalculatorTest
 *
 * @package Sonata\Test\Component\Currency\Types
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class CurrencyPriceCalculatorTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPrice()
    {
        $currencyPriceCalculator = new CurrencyPriceCalculator();

        $product = $this->getMock('Sonata\Component\Product\ProductInterface');

        $currency = new Currency();
        $currency->setLabel('EUR');

        $product->expects($this->any())->method('getCurrency')->will($this->returnValue($currency));
        $product->expects($this->once())->method('getPrice')->will($this->returnValue(42.0));

        $this->assertEquals(42.0, $currencyPriceCalculator->getPrice($product, $currency));
    }

    /**
     * @expectedException Sonata\Component\Currency\UnavailableForCurrencyException
     * @expectedExceptionMessage Product 'product_name' is not available for currency 'EUR'
     */
    public function testGetPriceException()
    {
        $currencyPriceCalculator = new CurrencyPriceCalculator();

        $product = $this->getMock('Sonata\Component\Product\ProductInterface');

        $currency = new Currency();
        $currency->setLabel('EUR');

        $product->expects($this->any())->method('getCurrency')->will($this->returnValue(null));
        $product->expects($this->any())->method('getPrice')->will($this->returnValue(42.0));
        $product->expects($this->once())->method('getName')->will($this->returnValue('product_name'));

        $currencyPriceCalculator->getPrice($product, $currency);
    }


}
