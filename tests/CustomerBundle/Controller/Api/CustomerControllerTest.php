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

use Symfony\Component\HttpFoundation\Request;

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
        $customerManager = $this->getMock('Sonata\Component\Customer\CustomerManagerInterface');
        $customerManager->expects($this->once())->method('getPager')->will($this->returnValue(array()));

        $paramFetcher = $this->getMock('FOS\RestBundle\Request\ParamFetcherInterface');
        $paramFetcher->expects($this->exactly(3))->method('get');
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue(array()));

        $this->assertEquals(array(), $this->createCustomerController(null, $customerManager)->getCustomersAction($paramFetcher));
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

        $this->assertEquals(array($order), $this->createCustomerController($customer, null, null, null, $order)->getCustomerOrdersAction(1));
    }

    public function testGetCustomerAddressesAction()
    {
        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');
        $address  = $this->getMock('Sonata\Component\Customer\AddressInterface');
        $customer->expects($this->once())->method('getAddresses')->will($this->returnValue(array($address)));

        $this->assertEquals(array($address), $this->createCustomerController($customer)->getCustomerAddressesAction(1));
    }

    public function testPostCustomerAction()
    {
        $customer = $this->getMock('Sonata\CustomerBundle\Model\CustomerInterface');

        $customerManager = $this->getMock('Sonata\Component\Customer\CustomerManagerInterface');
        $customerManager->expects($this->once())->method('save')->will($this->returnValue($customer));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('bind');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($customer));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createCustomerController(null, $customerManager, null, $formFactory)->postCustomerAction(new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPostCustomerInvalidAction()
    {
        $customer = $this->getMock('Sonata\CustomerBundle\Model\CustomerInterface');

        $customerManager = $this->getMock('Sonata\Component\Customer\CustomerManagerInterface');
        $customerManager->expects($this->never())->method('save')->will($this->returnValue($customer));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('bind');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createCustomerController(null, $customerManager, null, $formFactory)->postCustomerAction(new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testPostCustomerAddressAction()
    {
        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');
        $address = $this->getMock('Sonata\Component\Customer\AddressInterface');
        $address->expects($this->once())->method('setCustomer');

        $customerManager = $this->getMock('Sonata\Component\Customer\CustomerManagerInterface');

        $addressManager = $this->getMock('Sonata\Component\Customer\AddressManagerInterface');
        $addressManager->expects($this->once())->method('save')->will($this->returnValue($address));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('bind');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($address));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createCustomerController($customer, $customerManager, $addressManager, $formFactory)->postCustomerAddressAction(1, new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPostCustomerAddressInvalidAction()
    {
        $customer = $this->getMock('Sonata\CustomerBundle\Model\CustomerInterface');

        $customerManager = $this->getMock('Sonata\Component\Customer\CustomerManagerInterface');
        $customerManager->expects($this->never())->method('save')->will($this->returnValue($customer));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('bind');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createCustomerController(null, $customerManager, null, $formFactory)->postCustomerAction(new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testPutCustomerAction()
    {
        $customer = $this->getMock('Sonata\CustomerBundle\Model\CustomerInterface');

        $customerManager = $this->getMock('Sonata\Component\Customer\CustomerManagerInterface');
        $customerManager->expects($this->once())->method('findOneBy')->will($this->returnValue($customer));
        $customerManager->expects($this->once())->method('save')->will($this->returnValue($customer));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('bind');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($customer));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createCustomerController($customer, $customerManager, null, $formFactory)->putCustomerAction(1, new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPutCustomerInvalidAction()
    {
        $customer = $this->getMock('Sonata\CustomerBundle\Model\CustomerInterface');

        $customerManager = $this->getMock('Sonata\Component\Customer\CustomerManagerInterface');
        $customerManager->expects($this->once())->method('findOneBy')->will($this->returnValue($customer));
        $customerManager->expects($this->never())->method('save')->will($this->returnValue($customer));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('bind');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createCustomerController($customer, $customerManager, null, $formFactory)->putCustomerAction(1, new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testDeleteCustomerAction()
    {
        $customer = $this->getMock('Sonata\CustomerBundle\Model\CustomerInterface');

        $customerManager = $this->getMock('Sonata\Component\Customer\CustomerManagerInterface');
        $customerManager->expects($this->once())->method('findOneBy')->will($this->returnValue($customer));
        $customerManager->expects($this->once())->method('delete');

        $view = $this->createCustomerController($customer, $customerManager)->deleteCustomerAction(1);

        $this->assertEquals(array('deleted' => true), $view);
    }

    public function testDeleteCustomerInvalidAction()
    {
        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $customerManager = $this->getMock('Sonata\Component\Customer\CustomerManagerInterface');
        $customerManager->expects($this->once())->method('findOneBy')->will($this->returnValue(null));
        $customerManager->expects($this->never())->method('delete');

        $this->createCustomerController(null, $customerManager)->deleteCustomerAction(1);
    }

    /**
     * @param $customer
     * @param $customerManager
     * @param $addressManager
     * @param $formFactory
     * @param $order
     * @param $orderManager
     *
     * @return CustomerController
     */
    public function createCustomerController($customer = null, $customerManager = null, $addressManager = null, $formFactory = null, $order = null, $orderManager = null)
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
        if (null === $addressManager) {
            $addressManager = $this->getMock('Sonata\Component\Customer\AddressManagerInterface');
        }
        if (null !== $order) {
            $orderManager->expects($this->once())->method('findBy')->will($this->returnValue(array($order)));
        }
        if (null === $formFactory) {
            $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        }

        return new CustomerController($customerManager, $orderManager, $addressManager, $formFactory);
    }
}
