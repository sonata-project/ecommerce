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
use PHPUnit\Framework\TestCase;
use Sonata\Component\Generator\PostgresReference;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Anton Zlotnikov <exp.razor@gmail.com>
 */
class PostgresReferenceTest extends TestCase
{
    public function testInvoice(): void
    {
        $invoice = new InvoiceMock();
        $postgresReference = $this->generateNewObject();

        $this->expectException(\RuntimeException::class, 'The invoice is not persisted into the database');
        $postgresReference->invoice($invoice);

        $invoice->setId(13);

        try {
            $this->assertNull($postgresReference->invoice($invoice));
        } catch (\Exception $e) {
            $this->fail('->invoice() should return a NULL value but should now throw an \Exception');
        }
    }

    public function testOrder(): void
    {
        $order = new OrderMock();
        $postgresReference = $this->generateNewObject();

        $this->expectException(\RuntimeException::class, 'The order is not persisted into the database');
        $postgresReference->order($order);

        $order->setId(13);

        try {
            $this->assertNull($postgresReference->order($order));
        } catch (\Exception $e) {
            $this->fail('->order() should return a NULL value but should not throw an \Exception');
        }
    }

    /**
     * @return \Sonata\Component\Generator\PostgresReference
     */
    private function generateNewObject()
    {
        $metadata = new ClassMetadata('entityName');
        $metadata->table = ['name' => 'tableName'];

        $connection = $this->createMock(Connection::class);

        $em = $this->createMock(EntityManager::class);
        $em->expects($this->any())
            ->method('getClassMetadata')
            ->willReturn($metadata);

        $connection->expects($this->any())
            ->method('query')
            ->willReturn(new \PDOStatement());

        $registry = $this->createMock(RegistryInterface::class);
        $registry->expects($this->any())->method('getManager')->willReturn($em);
        $registry->expects($this->any())->method('getConnection')->willReturn($connection);

        return new PostgresReference($registry);
    }
}
