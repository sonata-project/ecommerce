<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\tests\Component\Currency;

use Sonata\Component\Currency\Currency;
use Sonata\Component\Currency\CurrencyDetector;
use Sonata\Component\Currency\CurrencyInterface;

/**
 * Test class for CurrencyDetector.
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class CurrencyDetectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CurrencyDetector
     */
    protected $object;

    /**
     * @var CurrencyInterface
     */
    protected $currency;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->currency = $this->getMock('Sonata\Component\Currency\CurrencyInterface');
        $this->currency->expects($this->any())
            ->method('getLabel')
            ->will($this->returnValue('EUR'))
        ;

        $currencyManager = $this->getMock('Sonata\Component\Currency\CurrencyManagerInterface');
        $currencyManager->expects($this->any())
            ->method('findOneByLabel')
            ->will($this->returnValue($this->currency))
        ;

        $this->object = new CurrencyDetector('EUR', $currencyManager);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Sonata\Component\Currency\CurrencyDetector::__construct
     */
    public function testConstruct()
    {
        $currency = new Currency();
        $currency->setLabel('EUR');

        $currencyManager = $this->getMock('Sonata\Component\Currency\CurrencyManagerInterface');
        $currencyManager->expects($this->any())
            ->method('findOneByLabel')
            ->will($this->returnValue($currency))
        ;

        $currencyDetector = new CurrencyDetector('EUR', $currencyManager);
        $this->assertSame('EUR', $currencyDetector->getCurrency()->getLabel());
    }

    /**
     * @covers Sonata\Component\Currency\CurrencyDetector::getCurrency
     */
    public function testGetCurrency()
    {
        $this->assertSame($this->currency->getLabel(), $this->object->getCurrency()->getLabel());
    }
}
