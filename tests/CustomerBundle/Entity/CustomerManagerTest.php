<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\tests\CustomerBundle\Entity;

use Sonata\CoreBundle\Test\EntityManagerMockFactory;
use Sonata\CustomerBundle\Entity\CustomerManager;

class CustomerManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPager()
    {
        $self = $this;
        $this
            ->getCustomerManager(function ($qb) use ($self) {
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('c.lastname'),
                    $self->equalTo('ASC')
                );
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(array()));
            })
            ->getPager(array(), 1);
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage Invalid sort field 'invalid' in 'Sonata\CustomerBundle\Entity\BaseCustomer' class
     */
    public function testGetPagerWithInvalidSort()
    {
        $self = $this;
        $this
            ->getCustomerManager(function ($qb) use ($self) {
            })
            ->getPager(array(), 1, 10, array('invalid' => 'ASC'));
    }

    public function testGetPagerWithMultipleSort()
    {
        $self = $this;
        $this
            ->getCustomerManager(function ($qb) use ($self) {
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
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(array()));
            })
            ->getPager(array(), 1, 10, array(
                'firstname' => 'ASC',
                'lastname' => 'DESC',
            ));
    }

    public function testGetPagerWithFakeCustomers()
    {
        $self = $this;
        $this
            ->getCustomerManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('c.isFake = :isFake'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(array('isFake' => true)));
            })
            ->getPager(array('is_fake' => true), 1);
    }

    public function testGetPagerWithNoFakeCustomer()
    {
        $self = $this;
        $this
            ->getCustomerManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('andWhere')->with($self->equalTo('c.isFake = :isFake'));
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(array('isFake' => false)));
            })
            ->getPager(array('is_fake' => false), 1);
    }

    protected function getCustomerManager($qbCallback)
    {
        if (version_compare(\PHPUnit_Runner_Version::id(), '5.0.0', '>=')) {
            $this->markTestSkipped('Not compatible with PHPUnit 5.');
        }

        $em = EntityManagerMockFactory::create($this, $qbCallback, array(
            'firstname',
            'lastname',
            'email',
        ));

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        return new CustomerManager('Sonata\CustomerBundle\Entity\BaseCustomer', $registry);
    }
}
