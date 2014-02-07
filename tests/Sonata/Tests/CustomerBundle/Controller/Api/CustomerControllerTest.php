<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\Test\CustomerBundle\Controller\Api;

use Sonata\CustomerBundle\Controller\Api\CustomerController;


/**
 * Class CustomerControllerTest
 *
 * @package Sonata\Test\CustomerBundle\Controller\Api
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class CustomerControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCustomersAction()
    {
        $customer        = $this->getMock('Sonata\Component\Customer\CustomerInterface');
        $customerManager = $this->getMock('Sonata\Component\Customer\CustomerManagerInterface');
        $customerManager->expects($this->once())->method('findBy')->will($this->returnValue(array($customer)));

        $paramFetcher = $this->getMock('FOS\RestBundle\Request\ParamFetcherInterface');
        $paramFetcher->expects($this->exactly(3))->method('get');
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue(array()));

        $this->assertEquals(array($customer), $this->createCustomerController(null, $customerManager)->getCustomersAction($paramFetcher));
    }

    public function testGetCustomerAction()
    {
        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');
        $this->assertEquals($customer, $this->createCustomerController($customer)->getCustomerAction(1));
    }

    /**
     * @expectedException        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Customer (42) not found
     */
    public function testGetCustomerActionNotFoundException()
    {
        $this->createCustomerController()->getCustomerAction(42);
    }

    public function testGetCustomerOrdersAction()
    {
        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');
        $order    = $this->getMock('Sonata\Component\Order\OrderInterface');

        $this->assertEquals(array($order), $this->createCustomerController($customer, null, $order)->getCustomerOrdersAction(1));
    }

    public function testGetCustomerAddressesAction()
    {
        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');
        $address  = $this->getMock('Sonata\Component\Customer\AddressInterface');
        $customer->expects($this->once())->method('getAddresses')->will($this->returnValue(array($address)));

        $this->assertEquals(array($address), $this->createCustomerController($customer)->getCustomerAddressesAction(1));
    }

    /**
     * @param $customer
     * @param $customerManager
     * @param $order
     * @param $orderManager
     *
     * @return CustomerController
     */
    public function createCustomerController($customer = null, $customerManager = null, $order = null, $orderManager = null)
    {
        if (null === $customerManager) {
            $customerManager = $this->getMock('Sonata\Component\Customer\CustomerManagerInterface');
        }
        if (null !== $customer) {
            $customerManager->expects($this->once())->method('findOneBy')->will($this->returnValue($customer));
        }

        if (null === $orderManager) {
            $orderManager = $this->getMock('Sonata\Component\Order\OrderManagerInterface');
        }
        if (null !== $order) {
            $orderManager->expects($this->once())->method('findBy')->will($this->returnValue(array($order)));
        }

        return new CustomerController($customerManager, $orderManager);
    }
}
