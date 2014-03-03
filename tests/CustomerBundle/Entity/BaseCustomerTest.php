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

use Sonata\Component\Customer\AddressInterface;
use Sonata\CustomerBundle\Entity\BaseAddress;
use Sonata\CustomerBundle\Entity\BaseCustomer;

class CustomerTest extends BaseCustomer
{
    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        // TODO: Implement getId() method.
    }

}

class AddressTest extends BaseAddress
{
    public function getId()
    {
        // TODO: Implement getId() method.
    }

}

/**
 * Class BaseCustomerTest
 *
 * @package Sonata\Tests\CustomerBundle\Entity
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class BaseCustomerTest extends \PHPUnit_Framework_TestCase
{
    public function testAddAddress()
    {
        $customer = new CustomerTest();
        $address = new AddressTest();
        $address->setType(AddressInterface::TYPE_BILLING);

        $customer->addAddress($address);
        $this->assertTrue($address->getCurrent());

        $address2 = new AddressTest();
        $address2->setType(AddressInterface::TYPE_BILLING);
        $customer->addAddress($address2);
        $this->assertFalse($address2->getCurrent());

        $address = new AddressTest();
        $address->setType(AddressInterface::TYPE_CONTACT);

        $customer->addAddress($address);
        $this->assertTrue($address->getCurrent());

        $address2 = new AddressTest();
        $address2->setType(AddressInterface::TYPE_CONTACT);
        $customer->addAddress($address2);
        $this->assertFalse($address2->getCurrent());
    }
}
