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

namespace Sonata\OrderBundle\Tests\Entity;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Sonata\Doctrine\Test\EntityManagerMockFactory;
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
    public function testGetClass(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $order = new OrderManager(Order::class, $registry);

        $this->assertSame(Order::class, $order->getClass());
    }

    public function testCreate(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $orderManager = new OrderManager(Order::class, $registry);

        $order = $orderManager->create();
        $this->assertInstanceOf(Order::class, $order);
    }

    public function testSave(): void
    {
        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects($this->exactly(2))->method('persist');
        $em->expects($this->once())->method('flush');

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $orderManager = new OrderManager(Order::class, $registry);

        $order = $this->createMock(Order::class);
        $order->expects($this->once())->method('getCustomer');

        $orderManager->save($order);
    }

    public function testDelete(): void
    {
        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects($this->once())->method('remove');
        $em->expects($this->once())->method('flush');

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $orderManager = new OrderManager(Order::class, $registry);

        $order = new Order();
        $orderManager->delete($order);
    }

    public function testGetPager(): void
    {
        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self): void {
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

    public function testGetPagerWithInvalidSort(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid sort field \'invalid\' in \'Sonata\\OrderBundle\\Entity\\BaseOrder\' class');

        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self): void {
            })
            ->getPager([], 1, 10, ['invalid' => 'ASC']);
    }

    public function testGetPagerWithMultipleSort(): void
    {
        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self): void {
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

    public function testGetPagerWithOpenOrders(): void
    {
        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['o']));
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('o.status = :status'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['status' => BaseOrder::STATUS_OPEN]));
            })
            ->getPager(['status' => BaseOrder::STATUS_OPEN], 1);
    }

    public function testGetPagerWithCanceledOrders(): void
    {
        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['o']));
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('o.status = :status'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['status' => BaseOrder::STATUS_CANCELLED]));
            })
            ->getPager(['status' => BaseOrder::STATUS_CANCELLED], 1);
    }

    public function testGetPagerWithErrorOrders(): void
    {
        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['o']));
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('o.status = :status'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['status' => BaseOrder::STATUS_ERROR]));
            })
            ->getPager(['status' => BaseOrder::STATUS_ERROR], 1);
    }

    public function testGetPagerWithPendingOrders(): void
    {
        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['o']));
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('o.status = :status'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['status' => BaseOrder::STATUS_PENDING]));
            })
            ->getPager(['status' => BaseOrder::STATUS_PENDING], 1);
    }

    public function testGetPagerWithStoppedOrders(): void
    {
        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['o']));
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('o.status = :status'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['status' => BaseOrder::STATUS_STOPPED]));
            })
            ->getPager(['status' => BaseOrder::STATUS_STOPPED], 1);
    }

    public function testGetPagerWithValidatedOrders(): void
    {
        $self = $this;
        $this
            ->getOrderManager(function ($qb) use ($self): void {
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

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        return new OrderManager(BaseOrder::class, $registry);
    }
}
