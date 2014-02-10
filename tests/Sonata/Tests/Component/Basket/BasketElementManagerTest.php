<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\Component\Basket;

use Sonata\Component\Basket\BasketElement;
use Sonata\Component\Basket\BasketElementManager;

/**
 * Class BasketElementManagerTest
 *
 * @package Sonata\Tests\Component\Basket
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class BasketElementManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateAndGetClass()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $basketEm = new BasketElementManager('Sonata\Component\Basket\BasketElement', $registry);

        $this->assertInstanceOf('Sonata\Component\Basket\BasketElement', $basketEm->create());
        $this->assertEquals('Sonata\Component\Basket\BasketElement', $basketEm->getClass());
    }

    public function testSave()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->once())->method('persist');
        $em->expects($this->once())->method('flush');

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $basketEm = new BasketElementManager('Sonata\Component\Basket\BasketElement', $registry);

        $basketElement = new BasketElement();
        $basketEm->save($basketElement);
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

        $basketEm = new BasketElementManager('Sonata\Component\Basket\BasketElement', $registry);

        $basketEm->findBy(array());
        $basketEm->findOneBy(array());
    }

    public function testDelete()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->once())->method('remove');
        $em->expects($this->once())->method('flush');

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $basketEm = new BasketElementManager('Sonata\Component\Basket\BasketElement', $registry);

        $basketElement = new BasketElement();
        $basketEm->delete($basketElement);
    }
}
