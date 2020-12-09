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

namespace Sonata\CustomerBundle\Tests\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Sonata\Component\Customer\AddressInterface;
use Sonata\Component\Customer\CustomerInterface;
use Sonata\CustomerBundle\Entity\AddressManager;
use Sonata\CustomerBundle\Entity\BaseAddress;
use Sonata\Doctrine\Test\EntityManagerMockFactoryTrait;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class AddressManagerTest extends TestCase
{
    use EntityManagerMockFactoryTrait;

    public function testSetCurrent(): void
    {
        $currentAddress = $this->createMock(AddressInterface::class);
        $currentAddress->expects($this->once())
            ->method('getCurrent')
            ->willReturn(true);
        $currentAddress->expects($this->once())->method('setCurrent');

        $custAddresses = [$currentAddress];

        $customer = $this->createMock(CustomerInterface::class);
        $customer->expects($this->once())->method('getAddressesByType')->willReturn($custAddresses);

        $address = $this->createMock(AddressInterface::class);
        $address->expects($this->once())->method('setCurrent');
        $address->expects($this->once())->method('getCustomer')->willReturn($customer);

        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects($this->any())->method('persist');

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects($this->any())->method('getManagerForClass')->willReturn($em);

        $addressManager = new AddressManager(AddressInterface::class, $registry);

        $addressManager->setCurrent($address);
    }

    public function testDelete(): void
    {
        $existingAddress = $this->createMock(AddressInterface::class);
        $existingAddress->expects($this->once())->method('setCurrent');
        $existingAddress->expects($this->once())->method('getId')->willReturn(42);

        $custAddresses = [$existingAddress, $this->createMock(AddressInterface::class)];

        $customer = $this->createMock(CustomerInterface::class);
        $customer->expects($this->once())
            ->method('getAddressesByType')
            ->willReturn($custAddresses);

        $address = $this->createMock(AddressInterface::class);
        $address->expects($this->once())->method('getCurrent')->willReturn(true);
        $address->expects($this->once())->method('getCustomer')->willReturn($customer);

        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects($this->exactly(1))->method('persist');

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects($this->any())->method('getManagerForClass')->willReturn($em);

        $addressManager = new AddressManager(AddressInterface::class, $registry);

        $addressManager->delete($address);
    }

    public function testGetPager(): void
    {
        $self = $this;
        $this
            ->getAddressManager(static function ($qb) use ($self): void {
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

    public function testGetPagerWithInvalidSort(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid sort field \'invalid\' in \'Sonata\\CustomerBundle\\Entity\\BaseAddress\' class');

        $self = $this;
        $this
            ->getAddressManager(static function ($qb): void {
            })
            ->getPager([], 1, 10, ['invalid' => 'ASC']);
    }

    public function testGetPagerWithMultipleSort(): void
    {
        $self = $this;
        $this
            ->getAddressManager(static function ($qb) use ($self): void {
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
        $em = $this->createEntityManagerMock($qbCallback, [
            'name',
            'firstname',
        ]);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects($this->any())->method('getManagerForClass')->willReturn($em);

        return new AddressManager(BaseAddress::class, $registry);
    }
}
