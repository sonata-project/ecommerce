<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Tests\Basket;

use Sonata\Component\Basket\Basket;
use Sonata\Component\Basket\BasketManager;
use Sonata\CoreBundle\Test\EntityManagerMockFactory;
use Sonata\Tests\Helpers\PHPUnit_Framework_TestCase;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class BasketManagerTest extends PHPUnit_Framework_TestCase
{
    public function testCreateAndGetClass()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();

        $registry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $basketMgr = new BasketManager('Sonata\Component\Basket\Basket', $registry);

        $this->assertInstanceOf('Sonata\Component\Basket\Basket', $basketMgr->create());
        $this->assertEquals('Sonata\Component\Basket\Basket', $basketMgr->getClass());
    }

    public function testSave()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->once())->method('persist');
        $em->expects($this->once())->method('flush');

        $registry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $basketMgr = new BasketManager('Sonata\Component\Basket\Basket', $registry);

        $basket = new Basket();
        $basketMgr->save($basket);
    }

    public function testFind()
    {
        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $repository->expects($this->once())->method('findOneBy');
        $repository->expects($this->once())->method('findBy');

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->any())->method('getRepository')->will($this->returnValue($repository));

        $registry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $basketMgr = new BasketManager('Sonata\Component\Basket\Basket', $registry);
        $basketMgr->findBy(array());
        $basketMgr->findOneBy(array());
    }

    public function testDelete()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->once())->method('remove');
        $em->expects($this->once())->method('flush');

        $registry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $basketMgr = new BasketManager('Sonata\Component\Basket\Basket', $registry);

        $basket = new Basket();
        $basketMgr->delete($basket);
    }

    public function testGetPager()
    {
        $self = $this;
        $this
            ->getBasketManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(array('b')));
                $qb->expects($self->never())->method('andWhere');
                $qb->expects($self->once())->method('orderBy')->with(
                    $self->equalTo('b.id'),
                    $self->equalTo('ASC')
                );
            })
            ->getPager(array(), 1);
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage Invalid sort field 'invalid' in 'Sonata\BasketBundle\Entity\BaseBasket' class
     */
    public function testGetPagerWithInvalidSort()
    {
        $self = $this;
        $this
            ->getBasketManager(function ($qb) use ($self) {
            })
            ->getPager(array(), 1, 10, array('invalid' => 'ASC'));
    }

    public function testGetPagerWithMultipleSort()
    {
        $self = $this;
        $this
            ->getBasketManager(function ($qb) use ($self) {
                $qb->expects($self->once())->method('getRootAliases')->will($self->returnValue(array('b')));
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
            ->getPager(array(), 1, 10, array(
                'id' => 'ASC',
                'locale' => 'DESC',
            ));
    }

    protected function getBasketManager($qbCallback)
    {
        if (version_compare(\PHPUnit_Runner_Version::id(), '5.0.0', '>=')) {
            $this->markTestSkipped('Not compatible with PHPUnit 5.');
        }

        $em = EntityManagerMockFactory::create($this, $qbCallback, array(
            'id',
            'locale',
        ));

        $registry = $this->createMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        return new BasketManager('Sonata\BasketBundle\Entity\BaseBasket', $registry);
    }
}
