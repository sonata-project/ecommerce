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
use Sonata\Component\Currency\CurrencyDataTransformer;
use Sonata\Component\Currency\CurrencyManagerInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class CurrencyDataTransformerTest extends TestCase
{
    private $currencyDataTransformer;
    private $currencyManager;

    protected function setUp(): void
    {
        $this->currencyManager = $this->createMock(CurrencyManagerInterface::class);

        $this->currencyDataTransformer = new CurrencyDataTransformer($this->currencyManager);
    }

    public function testTransform(): void
    {
        $currency = new Currency();
        $currency->setLabel('EUR');

        $this->assertSame('EUR', $this->currencyDataTransformer->transform($currency));
        $this->assertSame('EUR', $this->currencyDataTransformer->transform('EUR'));
    }

    public function testReverseTransform(): void
    {
        $currency = new Currency();
        $currency->setLabel('EUR');

        $this->currencyManager->expects($this->once())
            ->method('findOneByLabel')
            ->will($this->returnValue($currency));

        $this->assertNull($this->currencyDataTransformer->reverseTransform(null));
        $this->assertSame('EUR', $this->currencyDataTransformer->reverseTransform('EUR')->getLabel());
    }
}
