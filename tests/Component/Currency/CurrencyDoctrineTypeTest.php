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

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Sonata\Component\Currency\Currency;
use Sonata\Component\Currency\CurrencyDoctrineType;

class CurrencyDoctrineTypeTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (Type::hasType('currency')) {
            Type::overrideType('currency', 'Sonata\Component\Currency\CurrencyDoctrineType');
        } else {
            Type::addType('currency', 'Sonata\Component\Currency\CurrencyDoctrineType');
        }
    }

    public function testGetName()
    {
        $this->assertEquals("currency", Type::getType('currency')->getName());
    }

    public function testConvertToDatabaseValue()
    {
        $platform = new MockPlatform();

        $currency = new Currency();
        $currency->setLabel("EUR");

        $this->assertEquals(
            'EUR',
            Type::getType('currency')->convertToDatabaseValue($currency, $platform)
        );
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage 'currency' type only handles values of type Sonata\Component\Currency\CurrencyInterface ; value of type string given
     */
    public function testConvertToDatabaseValueException()
    {
        $platform = new MockPlatform();
        Type::getType('currency')->convertToDatabaseValue("EUR", $platform);
    }

    public function testConvertToPHPValue()
    {
        $platform = new MockPlatform();

        $currency = new Currency();
        $currency->setLabel("EUR");

        $this->assertEquals(
            $currency,
            Type::getType('currency')->convertToPHPValue('EUR', $platform)
        );
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage '42' is not a supported currency.
     */
    public function testConvertToPHPValueException()
    {
        $platform = new MockPlatform();
        Type::getType('currency')->convertToPHPValue('42', $platform);
    }

    public function testGetDefaultLength()
    {
        $platform = new MockPlatform();

        $this->assertEquals(
            3,
            Type::getType('currency')->getDefaultLength($platform)
        );
    }

    public function testGetSQLDeclaration()
    {
        $platform = new MockPlatform();

        $this->assertEquals("DUMMYVARCHAR()", Type::getType('currency')->getSQLDeclaration(array(), $platform));
    }
}

class MockPlatform extends \Doctrine\DBAL\Platforms\AbstractPlatform
{
    /**
     * Gets the SQL Snippet used to declare a BLOB column type.
     */
    public function getBlobTypeDeclarationSQL(array $field)
    {
        throw DBALException::notSupported(__METHOD__);
    }

    public function getBooleanTypeDeclarationSQL(array $columnDef) {}
    public function getIntegerTypeDeclarationSQL(array $columnDef) {}
    public function getBigIntTypeDeclarationSQL(array $columnDef) {}
    public function getSmallIntTypeDeclarationSQL(array $columnDef) {}
    public function _getCommonIntegerTypeDeclarationSQL(array $columnDef) {}

    public function getVarcharTypeDeclarationSQL(array $field)
    {
        return "DUMMYVARCHAR()";
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
    protected function initializeDoctrineTypeMappings()
    {
    }
    protected function getVarcharTypeDeclarationSQLSnippet($length, $fixed)
    {
    }
}
