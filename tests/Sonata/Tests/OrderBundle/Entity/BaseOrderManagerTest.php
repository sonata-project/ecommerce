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
use Application\Sonata\OrderBundle\Entity\OrderElement;

/**
 * Class CurrencyDataTransformerTest
 *
 * @package Sonata\Test\OrderBundle\Entity
 *
 * @author Xavier Coureau <xcoureau@ekino.com>
 */
class OrderManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetClass()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $orderManager = new OrderManager($em, 'Application\Sonata\OrderBundle\Entity\OrderElement');

        $this->assertEquals('Application\Sonata\OrderBundle\Entity\OrderElement', $orderManager->getClass());
        unset($orderManager, $em);
    }

    public function testCreate()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $orderManager = new OrderManager($em, 'Application\Sonata\OrderBundle\Entity\OrderElement');
        $orderElement = $orderManager->create();
        $this->assertInstanceOf('Application\Sonata\OrderBundle\Entity\OrderElement', $orderElement);
        unset($orderManager, $em, $orderElement);
    }

    public function testSave()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();

        $em->expects($this->exactly(2))->method('persist');
        $em->expects($this->once())->method('flush');

        $orderManager = new OrderManager($em, 'Application\Sonata\OrderBundle\Entity\OrderElement');
        $orderElement = $this->getMock('Sonata\Component\Order\OrderInterface');
        $orderManager->save($orderElement);
    }

    public function testDelete()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();

        $em->expects($this->once())->method('remove');
        $em->expects($this->once())->method('flush');

        $orderManager = new OrderManager($em, 'Application\Sonata\OrderBundle\Entity\OrderElement');
        $orderElement = $this->getMock('Sonata\Component\Order\OrderInterface');
        $orderManager->delete($orderElement);
    }

}