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

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;
use Sonata\Component\Currency\Currency;
use Sonata\Component\Currency\CurrencyDoctrineType;

class CurrencyDoctrineTypeTest extends TestCase
{
    public function setUp(): void
    {
        if (Type::hasType('currency')) {
            Type::overrideType('currency', CurrencyDoctrineType::class);
        } else {
            Type::addType('currency', CurrencyDoctrineType::class);
        }
    }

    public function testGetName(): void
    {
        $this->assertEquals('currency', Type::getType('currency')->getName());
    }

    public function testConvertToDatabaseValue(): void
    {
        $platform = new MockPlatform();

        $currency = new Currency();
        $currency->setLabel('EUR');

        $this->assertEquals(
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

        $this->assertEquals(
            $currency,
            Type::getType('currency')->convertToPHPValue('EUR', $platform)
        );
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

        $this->assertEquals(
            3,
            Type::getType('currency')->getDefaultLength($platform)
        );
    }

    public function testGetSQLDeclaration(): void
    {
        $platform = new MockPlatform();

        $this->assertEquals('DUMMYVARCHAR()', Type::getType('currency')->getSQLDeclaration([], $platform));
    }
}

class MockPlatform extends AbstractPlatform
{
    /**
     * Gets the SQL Snippet used to declare a BLOB column type.
     */
    public function getBlobTypeDeclarationSQL(array $field): void
    {
        throw DBALException::notSupported(__METHOD__);
    }

    public function getBooleanTypeDeclarationSQL(array $columnDef): void
    {
    }

    public function getIntegerTypeDeclarationSQL(array $columnDef): void
    {
    }

    public function getBigIntTypeDeclarationSQL(array $columnDef): void
    {
    }

    public function getSmallIntTypeDeclarationSQL(array $columnDef): void
    {
    }

    public function _getCommonIntegerTypeDeclarationSQL(array $columnDef): void
    {
    }

    public function getVarcharTypeDeclarationSQL(array $field)
    {
        return 'DUMMYVARCHAR()';
    }

    /** @override */
    public function getClobTypeDeclarationSQL(array $field)
    {
        return 'DUMMYCLOB';
    }

    public function getVarcharDefaultLength()
    {
        return 255;
    }

    public function getName()
    {
        return 'mock';
    }

    protected function initializeDoctrineTypeMappings(): void
    {
    }

    protected function getVarcharTypeDeclarationSQLSnippet($length, $fixed): void
    {
    }
}
