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

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Sonata\Component\Basket\BasketElement;
use Sonata\Component\Basket\BasketElementManager;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class BasketElementManagerTest extends TestCase
{
    public function testCreateAndGetClass(): void
    {
        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects(static::any())->method('getManagerForClass')->willReturn($em);

        $basketEm = new BasketElementManager(BasketElement::class, $registry);

        static::assertInstanceOf(BasketElement::class, $basketEm->create());
        static::assertSame(BasketElement::class, $basketEm->getClass());
    }

    public function testSave(): void
    {
        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects(static::once())->method('persist');
        $em->expects(static::once())->method('flush');

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects(static::any())->method('getManagerForClass')->willReturn($em);

        $basketEm = new BasketElementManager(BasketElement::class, $registry);

        $basketElement = new BasketElement();
        $basketEm->save($basketElement);
    }

    public function testFind(): void
    {
        $repository = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
        $repository->expects(static::once())->method('findOneBy');
        $repository->expects(static::once())->method('findBy');

        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects(static::any())->method('getRepository')->willReturn($repository);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects(static::any())->method('getManagerForClass')->willReturn($em);

        $basketEm = new BasketElementManager(BasketElement::class, $registry);

        $basketEm->findBy([]);
        $basketEm->findOneBy([]);
    }

    public function testDelete(): void
    {
        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects(static::once())->method('remove');
        $em->expects(static::once())->method('flush');

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects(static::any())->method('getManagerForClass')->willReturn($em);

        $basketEm = new BasketElementManager(BasketElement::class, $registry);

        $basketElement = new BasketElement();
        $basketEm->delete($basketElement);
    }
}
