<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Tests\Generator;

use Doctrine\ORM\Mapping\ClassMetadata;
use Sonata\Component\Generator\PostgresReference;
use Sonata\Tests\Helpers\PHPUnit_Framework_TestCase;

/**
 * @author Anton Zlotnikov <exp.razor@gmail.com>
 */
class PostgresReferenceTest extends PHPUnit_Framework_TestCase
{
    public function testInvoice()
    {
        $invoice = new InvoiceMock();
        $postgresReference = $this->generateNewObject();

        $this->expectException('\RuntimeException', 'The invoice is not persisted into the database');
        $postgresReference->invoice($invoice);

        $invoice->setId(13);

        try {
            $this->assertNull($postgresReference->invoice($invoice));
        } catch (\Exception $e) {
            $this->fail('->invoice() should return a NULL value but should now throw an \Exception');
        }
    }

    public function testOrder()
    {
        $order = new OrderMock();
        $postgresReference = $this->generateNewObject();

        $this->expectException('\RuntimeException', 'The order is not persisted into the database');
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
        $metadata->table = array('name' => 'tableName');

        $connection = $this->createMock('Doctrine\DBAL\Connection');

        $em = $this->createMock('Doctrine\ORM\EntityManager');
        $em->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue($metadata));

        $connection->expects($this->any())
            ->method('query')
            ->will($this->returnValue(new \PDOStatement()));

        $registry = $this->createMock('Symfony\Bridge\Doctrine\RegistryInterface');
        $registry->expects($this->any())->method('getManager')->will($this->returnValue($em));
        $registry->expects($this->any())->method('getConnection')->will($this->returnValue($connection));

        return new PostgresReference($registry);
    }
}
