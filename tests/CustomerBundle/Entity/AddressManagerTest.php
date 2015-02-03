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

use Sonata\CustomerBundle\Entity\AddressManager;

/**
 * Class AddressManagerTest
 *
 * @package Sonata\Tests\CustomerBundle\Entity
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class AddressManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testSetCurrent()
    {
        $currentAddress = $this->getMock('Sonata\Component\Customer\AddressInterface');
        $currentAddress->expects($this->once())
            ->method('getCurrent')
            ->will($this->returnValue(true));
        $currentAddress->expects($this->once())->method('setCurrent');

        $custAddresses = array($currentAddress);

        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');
        $customer->expects($this->once())->method('getAddressesByType')->will($this->returnValue($custAddresses));

        $address = $this->getMock('Sonata\Component\Customer\AddressInterface');
        $address->expects($this->once())->method('setCurrent');
        $address->expects($this->once())->method('getCustomer')->will($this->returnValue($customer));

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->any())->method('persist');

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $addressManager = new AddressManager('Sonata\Component\Customer\AddressInterface', $registry);

        $addressManager->setCurrent($address);
    }

    public function testDelete()
    {
        $existingAddress = $this->getMock('Sonata\Component\Customer\AddressInterface');
        $existingAddress->expects($this->once())->method('setCurrent');
        $existingAddress->expects($this->once())->method('getId')->will($this->returnValue(42));

        $custAddresses = array($existingAddress, $this->getMock('Sonata\Component\Customer\AddressInterface'));

        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');
        $customer->expects($this->once())
            ->method('getAddressesByType')
            ->will($this->returnValue($custAddresses));

        $address = $this->getMock('Sonata\Component\Customer\AddressInterface');
        $address->expects($this->once())->method('getCurrent')->will($this->returnValue(true));
        $address->expects($this->once())->method('getCustomer')->will($this->returnValue($customer));

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->exactly(1))->method('persist');

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $addressManager = new AddressManager('Sonata\Component\Customer\AddressInterface', $registry);

        $addressManager->delete($address);
    }


    protected function getAddressManager($qbCallback)
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
            'name',
            'firstname',
        )));

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->any())->method('getRepository')->will($this->returnValue($repository));
        $em->expects($this->any())->method('getClassMetadata')->will($this->returnValue($metadata));

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        return new AddressManager('Sonata\CustomerBundle\Entity\BaseAddress', $registry);
    }

    public function testGetPager()
    {
        $self = $this;
        $this
            ->getAddressManager(function ($qb) use ($self) {
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('a.name'),
                    $self->equalTo('ASC')
                );
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(array()));
            })
            ->getPager(array(), 1);
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage Invalid sort field 'invalid' in 'Sonata\CustomerBundle\Entity\BaseAddress' class
     */
    public function testGetPagerWithInvalidSort()
    {
        $self = $this;
        $this
            ->getAddressManager(function ($qb) use ($self) { })
            ->getPager(array(), 1, 10, array('invalid' => 'ASC'));
    }

    public function testGetPagerWithMultipleSort()
    {
        $self = $this;
        $this
            ->getAddressManager(function ($qb) use ($self) {
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->exactly(2))->method('orderBy')->with(
                    $self->logicalOr(
                        $self->equalTo('a.name'),
                        $self->equalTo('a.firstname')
                    ),
                    $self->logicalOr(
                        $self->equalTo('ASC'),
                        $self->equalTo('DESC')
                    )
                );
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo(array()));
            })
            ->getPager(array(), 1, 10, array(
                'name' => 'ASC',
                'firstname'  => 'DESC',
            ));
    }
}
