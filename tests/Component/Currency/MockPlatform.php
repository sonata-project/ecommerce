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

    public function getCurrentDatabaseExpression(): string
    {
        return '';
    }

    protected function initializeDoctrineTypeMappings(): void
    {
    }

    protected function getVarcharTypeDeclarationSQLSnippet($length, $fixed): void
    {
    }
}
