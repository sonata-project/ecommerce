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

namespace Sonata\Component\Tests\Generator;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Sonata\Component\Generator\MysqlReference;

/**
 * @author Xavier Coureau <xcoureau@ekino.com>
 */
class MysqlReferenceTest extends TestCase
{
    public function testInvoice(): void
    {
        $invoice = new InvoiceMock();
        $mysqlReference = $this->generateNewObject();

        try {
            $mysqlReference->invoice($invoice);
            $this->fail('->invoice() call should raise a \RuntimeException for a new entity');
        } catch (\Exception $e) {
            $this->assertInstanceOf(\RuntimeException::class, $e);
        }

        $invoice->setId(12);

        $this->assertNull($mysqlReference->invoice($invoice));
    }

    public function testOrder(): void
    {
        $order = new OrderMock();
        $mysqlReference = $this->generateNewObject();

        try {
            $mysqlReference->order($order);
            $this->fail('->order() call should raise a \RuntimeException for a new entity');
        } catch (\Exception $e) {
            $this->assertInstanceOf(\RuntimeException::class, $e);
        }

        $order->setId(12);

        $this->assertNull($mysqlReference->order($order));
    }

    /**
     * @return \Sonata\Component\Generator\MysqlReference
     */
    private function generateNewObject()
    {
        $metadata = new ClassMetadata('entityName');
        $metadata->table = ['name' => 'tableName'];

        $connection = $this->createMock(Connection::class);
        $statement = $this->createMock(\PDOStatement::class);
        $statement->expects($this->once())->method('fetch')->willReturn(false);

        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects($this->any())
            ->method('getClassMetadata')
            ->willReturn($metadata);

        $connection->expects($this->any())
            ->method('query')
            ->willReturn($statement);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects($this->any())->method('getManager')->willReturn($em);
        $registry->expects($this->any())->method('getConnection')->willReturn($connection);

        return new MysqlReference($registry);
    }
}
