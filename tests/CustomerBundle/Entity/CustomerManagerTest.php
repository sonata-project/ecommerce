<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\CustomerBundle\Entity;

use Sonata\CustomerBundle\Entity\CustomerManager;

/**
 * Class CustomerManagerTest
 *
 */
class CustomerManagerTest extends \PHPUnit_Framework_TestCase
{
    protected function getCustomerManager($qbCallback)
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
            'firstname',
            'lastname',
            'email',
        )));

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->any())->method('getRepository')->will($this->returnValue($repository));
        $em->expects($this->any())->method('getClassMetadata')->will($this->returnValue($metadata));

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));
        return new CustomerManager('Sonata\CustomerBundle\Entity\BaseCustomer', $registry);
    }

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
            ->getCustomerManager(function ($qb) use ($self) {})
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
                'lastname'  => 'DESC',
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
}
