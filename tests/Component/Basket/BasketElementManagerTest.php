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
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $basketEm = new BasketElementManager(BasketElement::class, $registry);

        $this->assertInstanceOf(BasketElement::class, $basketEm->create());
        $this->assertSame(BasketElement::class, $basketEm->getClass());
    }

    public function testSave(): void
    {
        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects($this->once())->method('persist');
        $em->expects($this->once())->method('flush');

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $basketEm = new BasketElementManager(BasketElement::class, $registry);

        $basketElement = new BasketElement();
        $basketEm->save($basketElement);
    }

    public function testFind(): void
    {
        $repository = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
        $repository->expects($this->once())->method('findOneBy');
        $repository->expects($this->once())->method('findBy');

        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects($this->any())->method('getRepository')->will($this->returnValue($repository));

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $basketEm = new BasketElementManager(BasketElement::class, $registry);

        $basketEm->findBy([]);
        $basketEm->findOneBy([]);
    }

    public function testDelete(): void
    {
        $em = $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
        $em->expects($this->once())->method('remove');
        $em->expects($this->once())->method('flush');

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $basketEm = new BasketElementManager(BasketElement::class, $registry);

        $basketElement = new BasketElement();
        $basketEm->delete($basketElement);
    }
}
