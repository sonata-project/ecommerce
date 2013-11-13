<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\Component\Payment;

use Sonata\Component\Payment\Selector;
use Sonata\CustomerBundle\Entity\BaseAddress;

class Address extends BaseAddress
{
    public function getId()
    {
        return $this->id;
    }
}

/**
 * Class SelectorTest
 *
 * @package Sonata\Test\Component\Payment
 *
 * @author Xavier Coureau <xcoureau@ekino.com>
 */
class SelectorTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPaymentPool()
    {
        $paymentPoolMethods = array('first method', 'second method');

        $paymentPool = $this->getMockBuilder('Sonata\Component\Payment\Pool')->getMock();
        $paymentPool->expects($this->any())
            ->method('getMethods')
            ->will($this->returnValue($paymentPoolMethods));

        $productPool = $this->getMockBuilder('Sonata\Component\Product\Pool')->getMock();

        $selector = new Selector($paymentPool, $productPool);
        $this->assertFalse($selector->getAvailableMethods());
        $this->assertEquals($paymentPoolMethods, $selector->getAvailableMethods(null, new Address()));
    }

    /**
     * @expectedException \Sonata\Component\Payment\PaymentNotFoundException
     * @expectedExceptionMessage Payment method with code 'not_existing' was not found
     */
    public function testGetPaymentException()
    {
        $paymentPoolMethods = array('first method', 'second method');

        $paymentPool = $this->getMockBuilder('Sonata\Component\Payment\Pool')->getMock();
        $paymentPool->expects($this->any())
            ->method('getMethods')
            ->will($this->returnValue($paymentPoolMethods));

        $productPool = $this->getMockBuilder('Sonata\Component\Product\Pool')->getMock();

        $selector = new Selector($paymentPool, $productPool);

        $selector->getPayment('not_existing');
    }
}
