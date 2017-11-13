<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\OrderBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Sonata\CoreBundle\Test\EntityManagerMockFactory;
use Sonata\OrderBundle\Entity\BaseOrder;
use Sonata\OrderBundle\Entity\OrderManager;

class Order extends BaseOrder
{
    /**
     * @return int the order id
     */
    public function getId()
    {
        // TODO: Implement getId() method.
    }
}

/**
 * @author Xavier Coureau <xcoureau@ekino.com>
 */
class OrderManagerTest extends TestCase
{
    public function testGetClass()
    {
        $registry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');
        $order = new OrderManager('Sonata\OrderBundle\Tests\Entity\Order', $registry);

        $this->assertEquals('Sonata\OrderBundle\Tests\Entity\Order', $order->getClass());
    }

    public function testCreate()
    {
        $registry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');
        $orderManager = new OrderManager('Sonata\OrderBundle\Tests\Entity\Order', $registry);

        $order = $orderManager->create();
        $this->assertInstanceOf('Sonata\OrderBundle\Tests\Entity\Order', $order);
    }

    public function testSave()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->exactly(2))->method('persist');
        $em->expects($this->once())->method('flush');

        $registry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $orderManager = new OrderManager('Sonata\OrderBundle\Tests\Entity\Order', $registry);

        $order = $this->createMock('Sonata\OrderBundle\Tests\Entity\Order');
        $order->expects($this->once())->method('getCustomer');

        $orderManager->save($order);
    }

    public function testDelete()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->once())->method('remove');
        $em->expects($this->once())->method('flush');

        $registry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $orderManager = new OrderManager('Sonata\OrderBundle\Tests\Entity\Order', $registry);

        $order = new Order();
        $orderManager->delete($order);
    }

    public function testGetPager()
    {
        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['o']));
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('o.reference'),
                    $self->equalTo('ASC')
                );
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo([]));
            })
            ->getPager([], 1);
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage Invalid sort field 'invalid' in 'Sonata\OrderBundle\Entity\BaseOrder' class
     */
    public function testGetPagerWithInvalidSort()
    {
        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self) {
            })
            ->getPager([], 1, 10, ['invalid' => 'ASC']);
    }

    public function testGetPagerWithMultipleSort()
    {
        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['o']));
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
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo([]));
            })
            ->getPager([], 1, 10, [
                'reference' => 'ASC',
                'username' => 'DESC',
            ]);
    }

    public function testGetPagerWithOpenOrders()
    {
        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['o']));
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('o.status = :status'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['status' => BaseOrder::STATUS_OPEN]));
            })
            ->getPager(['status' => BaseOrder::STATUS_OPEN], 1);
    }

    public function testGetPagerWithCanceledOrders()
    {
        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['o']));
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('o.status = :status'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['status' => BaseOrder::STATUS_CANCELLED]));
            })
            ->getPager(['status' => BaseOrder::STATUS_CANCELLED], 1);
    }

    public function testGetPagerWithErrorOrders()
    {
        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['o']));
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('o.status = :status'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['status' => BaseOrder::STATUS_ERROR]));
            })
            ->getPager(['status' => BaseOrder::STATUS_ERROR], 1);
    }

    public function testGetPagerWithPendingOrders()
    {
        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['o']));
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('o.status = :status'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['status' => BaseOrder::STATUS_PENDING]));
            })
            ->getPager(['status' => BaseOrder::STATUS_PENDING], 1);
    }

    public function testGetPagerWithStoppedOrders()
    {
        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['o']));
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('o.status = :status'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['status' => BaseOrder::STATUS_STOPPED]));
            })
            ->getPager(['status' => BaseOrder::STATUS_STOPPED], 1);
    }

    public function testGetPagerWithValidatedOrders()
    {
        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['o']));
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('o.status = :status'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['status' => BaseOrder::STATUS_VALIDATED]));
            })
            ->getPager(['status' => BaseOrder::STATUS_VALIDATED], 1);
    }

    protected function getOrderManager($qbCallback)
    {
        $em = EntityManagerMockFactory::create($this, $qbCallback, [
            'reference',
            'status',
            'username',
        ]);

        $registry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        return new OrderManager('Sonata\OrderBundle\Entity\BaseOrder', $registry);
    }
}
