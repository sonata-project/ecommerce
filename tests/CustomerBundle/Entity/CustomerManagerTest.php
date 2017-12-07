<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\CustomerBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Sonata\CoreBundle\Test\EntityManagerMockFactory;
use Sonata\CustomerBundle\Entity\CustomerManager;

class CustomerManagerTest extends TestCase
{
    public function testGetPager()
    {
        $self = $this;
        $this
            ->getCustomerManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['c']));
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('c.lastname'),
                    $self->equalTo('ASC')
                );
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo([]));
            })
            ->getPager([], 1);
    }

    public function testGetPagerWithInvalidSort()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid sort field \'invalid\' in \'Sonata\\CustomerBundle\\Entity\\BaseCustomer\' class');

        $self = $this;
        $this
            ->getCustomerManager(function ($qb) use ($self) {
            })
            ->getPager([], 1, 10, ['invalid' => 'ASC']);
    }

    public function testGetPagerWithMultipleSort()
    {
        $self = $this;
        $this
            ->getCustomerManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['c']));
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->exactly(2))->method('orderBy')->with(
                    $self->logicalOr(
                        $self->equalTo('c.firstname'),
                        $self->equalTo('c.lastname')
                    ),
                    $self->logicalOr(
                        $self->equalTo('ASC'),
                        $self->equalTo('DESC')
                    )
                );
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo([]));
            })
            ->getPager([], 1, 10, [
                'firstname' => 'ASC',
                'lastname' => 'DESC',
            ]);
    }

    public function testGetPagerWithFakeCustomers()
    {
        $self = $this;
        $this
            ->getCustomerManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['c']));
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('c.isFake = :isFake'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['isFake' => true]));
            })
            ->getPager(['is_fake' => true], 1);
    }

    public function testGetPagerWithNoFakeCustomer()
    {
        $self = $this;
        $this
            ->getCustomerManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['c']));
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('c.isFake = :isFake'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(['isFake' => false]));
            })
            ->getPager(['is_fake' => false], 1);
    }

    protected function getCustomerManager($qbCallback)
    {
        $em = EntityManagerMockFactory::create($this, $qbCallback, [
            'firstname',
            'lastname',
            'email',
        ]);

        $registry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        return new CustomerManager('Sonata\CustomerBundle\Entity\BaseCustomer', $registry);
    }
}
