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

use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;
use Sonata\Component\Currency\Currency;
use Sonata\Component\Currency\CurrencyDoctrineType;

class CurrencyDoctrineTypeTest extends TestCase
{
    protected function setUp(): void
    {
        if (Type::hasType('currency')) {
            Type::overrideType('currency', CurrencyDoctrineType::class);
        } else {
            Type::addType('currency', CurrencyDoctrineType::class);
        }
    }

    public function testGetName(): void
    {
        $this->assertSame('currency', Type::getType('currency')->getName());
    }

    public function testConvertToDatabaseValue(): void
    {
        $platform = new MockPlatform();

        $currency = new Currency();
        $currency->setLabel('EUR');

        $this->assertSame(
            'EUR',
            Type::getType('currency')->convertToDatabaseValue($currency, $platform)
        );
    }

    public function testConvertToDatabaseValueException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('\'currency\' type only handles values of type Sonata\\Component\\Currency\\CurrencyInterface ; value of type string given');

        $platform = new MockPlatform();
        Type::getType('currency')->convertToDatabaseValue('EUR', $platform);
    }

    public function testConvertToPHPValue(): void
    {
        $platform = new MockPlatform();

        $currency = new Currency();
        $currency->setLabel('EUR');

        $this->assertTrue($currency->equals(
            Type::getType('currency')->convertToPHPValue('EUR', $platform)
        ));
    }

    public function testConvertToPHPValueException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('\'42\' is not a supported currency.');

        $platform = new MockPlatform();
        Type::getType('currency')->convertToPHPValue('42', $platform);
    }

    public function testGetDefaultLength(): void
    {
        $platform = new MockPlatform();

        $this->assertSame(
            3,
            Type::getType('currency')->getDefaultLength($platform)
        );
    }

    public function testGetSQLDeclaration(): void
    {
        $platform = new MockPlatform();

        $this->assertSame('DUMMYVARCHAR()', Type::getType('currency')->getSQLDeclaration([], $platform));
    }
}
