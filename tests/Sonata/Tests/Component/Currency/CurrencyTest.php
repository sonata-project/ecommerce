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

/**
 * Class CurrencyTest
 *
 * @package Sonata\Test\Component\Currency\Types
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class CurrencyTest extends \PHPUnit_Framework_TestCase
{
    public function testGettersSetters()
    {
        $currency = new Currency();
        $currency->setLabel('EUR');

        $this->assertEquals('EUR', $currency->getLabel());
        $this->assertEquals('EUR', $currency->__toString());
    }

    public function testEquals()
    {
        $currency = new Currency();
        $currency->setLabel('EUR');

        $this->assertFalse($currency->equals(null));
        $this->assertFalse($currency->equals(new Currency()));
        $this->assertTrue($currency->equals($currency));
    }
}
