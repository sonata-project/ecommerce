<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Test\OrderBundle\Entity;

use Sonata\OrderBundle\Entity\BaseOrder;
use Sonata\OrderBundle\Entity\OrderManager;

class Order extends BaseOrder
{
    /**
     * @return integer the order id
     */
    public function getId()
    {
        // TODO: Implement getId() method.
    }

}

/**
 * Class OrderManagerTest
 *
 * @package Sonata\Test\OrderBundle\Entity
 *
 * @author Xavier Coureau <xcoureau@ekino.com>
 */
class OrderManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetClass()
    {
        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $order = new OrderManager('Sonata\Test\OrderBundle\Entity\Order', $registry);

        $this->assertEquals('Sonata\Test\OrderBundle\Entity\Order', $order->getClass());
    }

    public function testCreate()
    {
        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $orderManager = new OrderManager('Sonata\Test\OrderBundle\Entity\Order', $registry);

        $order = $orderManager->create();
        $this->assertInstanceOf('Sonata\Test\OrderBundle\Entity\Order', $order);
    }

    public function testSave()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->exactly(2))->method('persist');
        $em->expects($this->once())->method('flush');

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $orderManager = new OrderManager('Sonata\Test\OrderBundle\Entity\Order', $registry);

        $order = $this->getMock('Sonata\Test\OrderBundle\Entity\Order');
        $order->expects($this->once())->method('getCustomer');

        $orderManager->save($order);
    }

    public function testDelete()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->once())->method('remove');
        $em->expects($this->once())->method('flush');

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $orderManager = new OrderManager('Sonata\Test\OrderBundle\Entity\Order', $registry);

        $order = new Order();
        $orderManager->delete($order);
    }

    protected function getOrderManager($qbCallback)
    {
        $query = $this->getMockForAbstractClass('Doctrine\ORM\AbstractQuery', array(), '', false, true, true, array('execute'));
        $query->expects($this->any())->method('execute')->will($this->returnValue(true));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')->disableOriginalConstructor()->getMock();
        $qb->expects($this->any())->method('select')->will($this->returnValue($qb));
        $qb->expects($this->any())->method('getQuery')->will($this->returnValue($query));

        $qbCallback($qb);

        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $repository->expects($this->any())->method('createQueryBuilder')->will($this->returnValue($qb));

        $metadata = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $metadata->expects($this->any())->method('getFieldNames')->will($this->returnValue(array(
            'reference',
            'status',
            'username',
        )));

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->any())->method('getRepository')->will($this->returnValue($repository));
        $em->expects($this->any())->method('getClassMetadata')->will($this->returnValue($metadata));

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        return new OrderManager('Sonata\OrderBundle\Entity\BaseOrder', $registry);
    }

    public function testGetPager()
    {
        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self) {
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('o.reference'),
                    $self->equalTo('ASC')
                );
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(array()));
            })
            ->getPager(array(), 1);
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage Invalid sort field 'invalid' in 'Sonata\OrderBundle\Entity\BaseOrder' class
     */
    public function testGetPagerWithInvalidSort()
    {
        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self) { })
            ->getPager(array(), 1, 10, array('invalid' => 'ASC'));
    }

    public function testGetPagerWithMultipleSort()
    {
        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self) {
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->exactly(2))->method('orderBy')->with(
                    $self->logicalOr(
                        $self->equalTo('o.reference'),
                        $self->equalTo('o.username')
                    ),
                    $self->logicalOr(
                        $self->equalTo('ASC'),
                        $self->equalTo('DESC')
                    )
                );
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(array()));
            })
            ->getPager(array(), 1, 10, array(
                'reference' => 'ASC',
                'username'  => 'DESC',
            ));
    }

    public function testGetPagerWithOpenOrders()
    {
        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('o.status = :status'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(array('status' => BaseOrder::STATUS_OPEN)));
            })
            ->getPager(array('status' => BaseOrder::STATUS_OPEN), 1);
    }

    public function testGetPagerWithCanceledOrders()
    {
        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('o.status = :status'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(array('status' => BaseOrder::STATUS_CANCELLED)));
            })
            ->getPager(array('status' => BaseOrder::STATUS_CANCELLED), 1);
    }

    public function testGetPagerWithErrorOrders()
    {
        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('o.status = :status'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(array('status' => BaseOrder::STATUS_ERROR)));
            })
            ->getPager(array('status' => BaseOrder::STATUS_ERROR), 1);
    }

    public function testGetPagerWithPendingOrders()
    {
        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('o.status = :status'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(array('status' => BaseOrder::STATUS_PENDING)));
            })
            ->getPager(array('status' => BaseOrder::STATUS_PENDING), 1);
    }

    public function testGetPagerWithStoppedOrders()
    {
        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('o.status = :status'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(array('status' => BaseOrder::STATUS_STOPPED)));
            })
            ->getPager(array('status' => BaseOrder::STATUS_STOPPED), 1);
    }

    public function testGetPagerWithValidatedOrders()
    {
        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('o.status = :status'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(array('status' => BaseOrder::STATUS_VALIDATED)));
            })
            ->getPager(array('status' => BaseOrder::STATUS_VALIDATED), 1);
    }
}
