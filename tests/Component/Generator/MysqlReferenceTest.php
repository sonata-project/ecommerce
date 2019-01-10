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
use Doctrine\ORM\EntityManager as BaseEntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Sonata\Component\Generator\MysqlReference;
use Sonata\InvoiceBundle\Entity\BaseInvoice;
use Sonata\OrderBundle\Entity\BaseOrder;
use Symfony\Bridge\Doctrine\RegistryInterface;

class EntityManager extends BaseEntityManager
{
    public function getClassMetadata($className)
    {
        $obj = new \stdClass();
        $obj->table['name'] = 'test';

        return $obj;
    }
}

class InvoiceMock extends BaseInvoice
{
    protected $id;

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns id.
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }
}

class OrderMock extends BaseOrder
{
    protected $id;

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int the order id
     */
    public function getId()
    {
        return $this->id;
    }
}

/**
 * @author Xavier Coureau <xcoureau@ekino.com>
 */
class MysqlReferenceTest extends TestCase
{
    public function testInvoice()
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

        try {
            $this->assertNull($mysqlReference->invoice($invoice));
        } catch (\Exception $e) {
            $this->fail('->invoice() should return a NULL value but should now throw an \Exception');
        }
    }

    public function testOrder()
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

        try {
            $this->assertNull($mysqlReference->order($order));
        } catch (\Exception $e) {
            $this->fail('->order() should return a NULL value but should not throw an \Exception');
        }
    }

    /**
     * @return \Sonata\Component\Generator\MysqlReference
     */
    private function generateNewObject()
    {
        $metadata = new ClassMetadata('entityName');
        $metadata->table = ['name' => 'tableName'];

        $connection = $this->createMock(Connection::class);

        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue($metadata));

        $connection->expects($this->any())
            ->method('query')
            ->will($this->returnValue(new \PDOStatement()));

        $registry = $this->createMock(RegistryInterface::class);
        $registry->expects($this->any())->method('getManager')->will($this->returnValue($em));
        $registry->expects($this->any())->method('getConnection')->will($this->returnValue($connection));

        return new MysqlReference($registry);
    }
}
