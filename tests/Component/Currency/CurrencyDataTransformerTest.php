<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Test\Component\Currency\Types;

use Sonata\Component\Currency\Currency;
use Sonata\Component\Currency\CurrencyDataTransformer;

/**
 * Class CurrencyDataTransformerTest.
 *
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class CurrencyDataTransformerTest extends \PHPUnit_Framework_TestCase
{
    private $currencyDataTransformer;
    private $currencyManager;

    protected function setUp()
    {
        $this->currencyManager = $this->getMock('Sonata\Component\Currency\CurrencyManagerInterface');

        $this->currencyDataTransformer = new CurrencyDataTransformer($this->currencyManager);
    }

    public function testTransform()
    {
        $currency = new Currency();
        $currency->setLabel('EUR');

        $this->assertSame('EUR', $this->currencyDataTransformer->transform($currency));
        $this->assertSame('EUR', $this->currencyDataTransformer->transform('EUR'));
    }

    public function testReverseTransform()
    {
        $currency = new Currency();
        $currency->setLabel('EUR');

        $this->currencyManager->expects($this->once())
            ->method('findOneByLabel')
            ->will($this->returnValue($currency));

        $this->assertSame(null, $this->currencyDataTransformer->reverseTransform(null));
        $this->assertSame('EUR', $this->currencyDataTransformer->reverseTransform('EUR')->getLabel());
    }
}
