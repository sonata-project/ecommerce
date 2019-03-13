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

namespace Sonata\Component\Tests\Basket;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Sonata\BasketBundle\Entity\BaseBasket;
use Sonata\Component\Basket\Basket;
use Sonata\Component\Basket\BasketManager;
use Sonata\Doctrine\Test\EntityManagerMockFactory;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class BasketManagerTest extends TestCase
{
    public function testCreateAndGetClass(): void
    {
        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $basketMgr = new BasketManager(Basket::class, $registry);

        $this->assertInstanceOf(Basket::class, $basketMgr->create());
        $this->assertSame(Basket::class, $basketMgr->getClass());
    }

    public function testSave(): void
    {
        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects($this->once())->method('persist');
        $em->expects($this->once())->method('flush');

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $basketMgr = new BasketManager(Basket::class, $registry);

        $basket = new Basket();
        $basketMgr->save($basket);
    }

    public function testFind(): void
    {
        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->once())->method('findOneBy');
        $repository->expects($this->once())->method('findBy');

        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects($this->any())->method('getRepository')->will($this->returnValue($repository));

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $basketMgr = new BasketManager(Basket::class, $registry);
        $basketMgr->findBy([]);
        $basketMgr->findOneBy([]);
    }

    public function testDelete(): void
    {
        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects($this->once())->method('remove');
        $em->expects($this->once())->method('flush');

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $basketMgr = new BasketManager(Basket::class, $registry);

        $basket = new Basket();
        $basketMgr->delete($basket);
    }

    public function testGetPager(): void
    {
        $self = $this;
        $this
            ->getBasketManager(function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['b']));
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('b.id'),
                    $self->equalTo('ASC')
                );
            })
            ->getPager([], 1);
    }

    public function testGetPagerWithInvalidSort(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid sort field \'invalid\' in \'Sonata\\BasketBundle\\Entity\\BaseBasket\' class');

        $self = $this;
        $this
            ->getBasketManager(function ($qb) use ($self): void {
            })
            ->getPager([], 1, 10, ['invalid' => 'ASC']);
    }

    public function testGetPagerWithMultipleSort(): void
    {
        $self = $this;
        $this
            ->getBasketManager(function ($qb) use ($self): void {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(['b']));
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->exactly(2))->method('orderBy')->with(
                    $self->logicalOr(
                        $self->equalTo('b.id'),
                        $self->equalTo('b.locale')
                    ),
                    $self->logicalOr(
                        $self->equalTo('ASC'),
                        $self->equalTo('DESC')
                    )
                );
            })
            ->getPager([], 1, 10, [
                'id' => 'ASC',
                'locale' => 'DESC',
            ]);
    }

    protected function getBasketManager($qbCallback)
    {
        $em = EntityManagerMockFactory::create($this, $qbCallback, [
            'id',
            'locale',
        ]);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        return new BasketManager(BaseBasket::class, $registry);
    }
}
