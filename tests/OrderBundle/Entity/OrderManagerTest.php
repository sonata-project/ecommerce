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

use Sonata\OrderBundle\Entity\BaseOrder;
use Sonata\OrderBundle\Entity\OrderManager;

class Order extends BaseOrder
{
    /**
     * @return integer the order id
     */
    public function getId()
    {
        // TODO: Implement getId() method.
    }

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
        $order = new OrderManager('Sonata\Test\OrderBundle\Entity\Order', $registry);

        $this->assertEquals('Sonata\Test\OrderBundle\Entity\Order', $order->getClass());
    }

    public function testCreate()
    {
        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $orderManager = new OrderManager('Sonata\Test\OrderBundle\Entity\Order', $registry);

        $order = $orderManager->create();
        $this->assertInstanceOf('Sonata\Test\OrderBundle\Entity\Order', $order);
    }

    public function testSave()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->exactly(2))->method('persist');
        $em->expects($this->once())->method('flush');

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $orderManager = new OrderManager('Sonata\Test\OrderBundle\Entity\Order', $registry);

        $order = $this->getMock('Sonata\Test\OrderBundle\Entity\Order');
        $order->expects($this->once())->method('getCustomer');

        $orderManager->save($order);
    }

    public function testDelete()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($this->once())->method('remove');
        $em->expects($this->once())->method('flush');

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $registry->expects($this->any())->method('getManagerForClass')->will($this->returnValue($em));

        $orderManager = new OrderManager('Sonata\Test\OrderBundle\Entity\Order', $registry);

        $order = new Order();
        $orderManager->delete($order);
    }
}
