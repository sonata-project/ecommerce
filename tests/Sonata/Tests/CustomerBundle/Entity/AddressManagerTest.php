<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\Tests\CustomerBundle\Entity;

use Sonata\CustomerBundle\Entity\AddressManager;


/**
 * Class AddressManagerTest
 *
 * @package Sonata\Tests\CustomerBundle\Entity
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class AddressManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testSetCurrent()
    {
        $currentAddress = $this->getMock('Sonata\Component\Customer\AddressInterface');
        $currentAddress->expects($this->once())
            ->method('getCurrent')
            ->will($this->returnValue(true));
        $currentAddress->expects($this->once())->method('setCurrent');

        $custAddresses = array($currentAddress);

        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');
        $customer->expects($this->once())
            ->method('getAddressesByType')
            ->will($this->returnValue($custAddresses));

        $address = $this->getMock('Sonata\Component\Customer\AddressInterface');
        $address->expects($this->once())->method('setCurrent');
        $address->expects($this->once())->method('getCustomer')->will($this->returnValue($customer));

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();

        $addressManager = new AddressManager($em, "");

        $addressManager->setCurrent($address);
    }

    public function testDelete()
    {
        $existingAddress = $this->getMock('Sonata\Component\Customer\AddressInterface');
        $existingAddress->expects($this->once())->method('setCurrent');
        $existingAddress->expects($this->once())->method('getId')->will($this->returnValue(42));

        $custAddresses = array($existingAddress, $this->getMock('Sonata\Component\Customer\AddressInterface'));

        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');
        $customer->expects($this->once())
            ->method('getAddressesByType')
            ->will($this->returnValue($custAddresses));

        $address = $this->getMock('Sonata\Component\Customer\AddressInterface');
        $address->expects($this->once())->method('getCurrent')->will($this->returnValue(true));
        $address->expects($this->once())->method('getCustomer')->will($this->returnValue($customer));

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();

        $addressManager = new AddressManager($em, "");

        $addressManager->delete($address);
    }
}
