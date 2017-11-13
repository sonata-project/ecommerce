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
use Sonata\Component\Currency\CurrencyDataTransformer;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class CurrencyDataTransformerTest extends TestCase
{
    private $currencyDataTransformer;
    private $currencyManager;

    protected function setUp()
    {
        $this->currencyManager = $this->createMock('Sonata\Component\Currency\CurrencyManagerInterface');

        $this->currencyDataTransformer = new CurrencyDataTransformer($this->currencyManager);
    }

    public function testTransform()
    {
        $currency = new Currency();
        $currency->setLabel('EUR');

        $this->assertEquals('EUR', $this->currencyDataTransformer->transform($currency));
        $this->assertEquals('EUR', $this->currencyDataTransformer->transform('EUR'));
    }

    public function testReverseTransform()
    {
        $currency = new Currency();
        $currency->setLabel('EUR');

        $this->currencyManager->expects($this->once())
            ->method('findOneByLabel')
            ->will($this->returnValue($currency));

        $this->assertEquals(null, $this->currencyDataTransformer->reverseTransform(null));
        $this->assertEquals('EUR', $this->currencyDataTransformer->reverseTransform('EUR')->getLabel());
    }
}
