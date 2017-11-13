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
use Sonata\CustomerBundle\Entity\AddressManager;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class AddressManagerTest extends TestCase
{
    public function testSetCurrent()
    {
        $currentAddress = $this->createMock('Sonata\Component\Customer\AddressInterface');
        $currentAddress->expects($this->once())
            ->method('getCurrent')
            ->will($this->returnValue(true));
        $currentAddress->expects($this->once())->method('setCurrent');

        $custAddresses = [$currentAddress];

        $customer = $this->createMock('Sonata\Component\Customer\CustomerInterface');
        $customer->expects($this->once())->method('getAddressesByType')->will($this->returnValue($custAddresses));

        $address = $this->createMock('Sonata\Component\Customer\AddressInterface');
        $address->expects($this->once())->method('setCurrent');
        $address->expects($this->once())->method('getCustomer')->will($this->returnValue($customer));

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->any())->method('persist');

        $registry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $addressManager = new AddressManager('Sonata\Component\Customer\AddressInterface', $registry);

        $addressManager->setCurrent($address);
    }

    public function testDelete()
    {
        $existingAddress = $this->createMock('Sonata\Component\Customer\AddressInterface');
        $existingAddress->expects($this->once())->method('setCurrent');
        $existingAddress->expects($this->once())->method('getId')->will($this->returnValue(42));

        $custAddresses = [$existingAddress, $this->createMock('Sonata\Component\Customer\AddressInterface')];

        $customer = $this->createMock('Sonata\Component\Customer\CustomerInterface');
        $customer->expects($this->once())
            ->method('getAddressesByType')
            ->will($this->returnValue($custAddresses));

        $address = $this->createMock('Sonata\Component\Customer\AddressInterface');
        $address->expects($this->once())->method('getCurrent')->will($this->returnValue(true));
        $address->expects($this->once())->method('getCustomer')->will($this->returnValue($customer));

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->exactly(1))->method('persist');

        $registry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $addressManager = new AddressManager('Sonata\Component\Customer\AddressInterface', $registry);

        $addressManager->delete($address);
    }

    public function testGetPager()
    {
        $self = $this;
        $this
            ->getAddressManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['a']));
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('a.name'),
                    $self->equalTo('ASC')
                );
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo([]));
            })
            ->getPager([], 1);
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage Invalid sort field 'invalid' in 'Sonata\CustomerBundle\Entity\BaseAddress' class
     */
    public function testGetPagerWithInvalidSort()
    {
        $self = $this;
        $this
            ->getAddressManager(function ($qb) use ($self) {
            })
            ->getPager([], 1, 10, ['invalid' => 'ASC']);
    }

    public function testGetPagerWithMultipleSort()
    {
        $self = $this;
        $this
            ->getAddressManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['a']));
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
                $qb->expects($self->once())->method('setParameters')->with($self->equalTo([]));
            })
            ->getPager([], 1, 10, [
                'name' => 'ASC',
                'firstname' => 'DESC',
            ]);
    }

    protected function getAddressManager($qbCallback)
    {
        $em = EntityManagerMockFactory::create($this, $qbCallback, [
            'name',
            'firstname',
        ]);

        $registry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        return new AddressManager('Sonata\CustomerBundle\Entity\BaseAddress', $registry);
    }
}
