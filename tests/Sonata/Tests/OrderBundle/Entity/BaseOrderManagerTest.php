<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Test\OrderBundle\Entity;

use Sonata\OrderBundle\Entity\OrderManager;

class OrderElement
{

}

/**
 * Class OrderManagerTest
 *
 * @package Sonata\Test\OrderBundle\Entity
 *
 * @author Xavier Coureau <xcoureau@ekino.com>
 */
class OrderManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetClass()
    {
        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $orderManager = new OrderManager('Sonata\Test\OrderBundle\Entity\OrderElement', $registry);

        $this->assertEquals('Sonata\Test\OrderBundle\Entity\OrderElement', $orderManager->getClass());

        unset($orderManager, $em);
    }

    public function testCreate()
    {
        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $orderManager = new OrderManager('Sonata\Test\OrderBundle\Entity\OrderElement', $registry);

        $orderElement = $orderManager->create();
        $this->assertInstanceOf('Sonata\Test\OrderBundle\Entity\OrderElement', $orderElement);

        unset($orderManager, $em, $orderElement);
    }

    public function testSave()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->exactly(2))->method('persist');
        $em->expects($this->once())->method('flush');

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $orderManager = new OrderManager('Sonata\Test\OrderBundle\Entity\OrderElement', $registry);
        $orderElement = $this->getMock('Sonata\Component\Order\OrderInterface');
        $orderManager->save($orderElement);

        unset($em, $orderManager, $orderElement);
    }

    public function testDelete()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->once())->method('remove');
        $em->expects($this->once())->method('flush');

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $orderManager = new OrderManager('Sonata\Test\OrderBundle\Entity\OrderElement', $registry);

        $orderElement = $this->getMock('Sonata\Component\Order\OrderInterface');
        $orderManager->delete($orderElement);

        unset($em, $orderManager, $orderElement);
    }

}
