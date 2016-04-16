<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\tests\Component\Basket;

use Sonata\Component\Basket\Basket;
use Sonata\Component\Basket\BasketManager;

/**
 * Class BasketManagerTest.
 *
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class BasketManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateAndGetClass()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $basketMgr = new BasketManager('Sonata\Component\Basket\Basket', $registry);

        $this->assertInstanceOf('Sonata\Component\Basket\Basket', $basketMgr->create());
        $this->assertSame('Sonata\Component\Basket\Basket', $basketMgr->getClass());
    }

    public function testSave()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->once())->method('persist');
        $em->expects($this->once())->method('flush');

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
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

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
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

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $basketMgr = new BasketManager('Sonata\Component\Basket\Basket', $registry);

        $basket = new Basket();
        $basketMgr->delete($basket);
    }
}
