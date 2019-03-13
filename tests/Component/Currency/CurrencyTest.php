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

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class CurrencyTest extends TestCase
{
    public function testGettersSetters(): void
    {
        $currency = new Currency();
        $currency->setLabel('EUR');

        $this->assertSame('EUR', $currency->getLabel());
        $this->assertSame('EUR', $currency->__toString());
    }

    public function testEquals(): void
    {
        $currency = new Currency();
        $currency->setLabel('EUR');

        $this->assertFalse($currency->equals(null));
        $this->assertFalse($currency->equals(new Currency()));
        $this->assertTrue($currency->equals($currency));
    }
}
