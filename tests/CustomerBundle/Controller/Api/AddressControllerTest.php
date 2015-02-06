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

use Sonata\CustomerBundle\Controller\Api\AddressController;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class AddressControllerTest
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class AddressControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAddressesAction()
    {
        $addressManager = $this->getMock('Sonata\Component\Customer\AddressManagerInterface');
        $addressManager->expects($this->once())->method('getPager')->will($this->returnValue(array()));

        $paramFetcher = $this->getMock('FOS\RestBundle\Request\ParamFetcherInterface');
        $paramFetcher->expects($this->exactly(3))->method('get');
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue(array()));

        $this->assertEquals(array(), $this->createAddressController(null, $addressManager)->getAddressesAction($paramFetcher));
    }

    public function testGetAddressAction()
    {
        $address = $this->getMock('Sonata\Component\Customer\AddressInterface');

        $this->assertEquals($address, $this->createAddressController($address)->getAddressAction(1));
    }

    /**
     * @expectedException        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Address (42) not found
     */
    public function testGetAddressActionNotFoundException()
    {
        $this->createAddressController()->getAddressAction(42);
    }

    public function testPostAddressAction()
    {
        $address = $this->getMock('Sonata\AddressBundle\Model\AddressInterface');

        $addressManager = $this->getMock('Sonata\Component\Customer\AddressManagerInterface');
        $addressManager->expects($this->once())->method('save')->will($this->returnValue($address));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('bind');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($address));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createAddressController(null, $addressManager, $formFactory)->postAddressAction(new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPostCustomerInvalidAction()
    {
        $address = $this->getMock('Sonata\CustomerBundle\Model\AddressInterface');

        $addressManager = $this->getMock('Sonata\Component\Customer\AddressManagerInterface');
        $addressManager->expects($this->never())->method('save')->will($this->returnValue($address));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('bind');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createAddressController(null, $addressManager, $formFactory)->postAddressAction(new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testPutAddressAction()
    {
        $address = $this->getMock('Sonata\CustomerBundle\Model\AddressInterface');

        $addressManager = $this->getMock('Sonata\Component\Customer\AddressManagerInterface');
        $addressManager->expects($this->once())->method('findOneBy')->will($this->returnValue($address));
        $addressManager->expects($this->once())->method('save')->will($this->returnValue($address));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('bind');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($address));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createAddressController($address, $addressManager, $formFactory)->putAddressAction(1, new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPutAddressInvalidAction()
    {
        $address = $this->getMock('Sonata\CustomerBundle\Model\AddressInterface');

        $addressManager = $this->getMock('Sonata\Component\Customer\AddressManagerInterface');
        $addressManager->expects($this->once())->method('findOneBy')->will($this->returnValue($address));
        $addressManager->expects($this->never())->method('save')->will($this->returnValue($address));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('bind');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createAddressController($address, $addressManager, $formFactory)->putAddressAction(1, new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testDeleteAddressAction()
    {
        $address = $this->getMock('Sonata\CustomerBundle\Model\AddressInterface');

        $addressManager = $this->getMock('Sonata\Component\Customer\AddressManagerInterface');
        $addressManager->expects($this->once())->method('findOneBy')->will($this->returnValue($address));
        $addressManager->expects($this->once())->method('delete');

        $view = $this->createAddressController($address, $addressManager)->deleteAddressAction(1);

        $this->assertEquals(array('deleted' => true), $view);
    }

    public function testDeleteAddressInvalidAction()
    {
        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $addressManager = $this->getMock('Sonata\Component\Customer\AddressManagerInterface');
        $addressManager->expects($this->once())->method('findOneBy')->will($this->returnValue(null));
        $addressManager->expects($this->never())->method('delete');

        $this->createAddressController(null, $addressManager)->deleteAddressAction(1);
    }

    /**
     * Returns address controller
     *
     * @param \Sonata\Component\Customer\AddressInterface        $address
     * @param \Sonata\Component\Customer\AddressManagerInterface $addressManager
     * @param \Symfony\Component\Form\FormFactory                $formFactory
     *
     * @return AddressController
     */
    public function createAddressController($address = null, $addressManager = null, $formFactory = null)
    {
        if (null === $addressManager) {
            $addressManager = $this->getMock('Sonata\Component\Customer\AddressManagerInterface');

            if ($address) {
                $addressManager->expects($this->once())->method('findOneBy')->will($this->returnValue($address));
            }
        }

        if (null === $formFactory) {
            $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        }

        return new AddressController($addressManager, $formFactory);
    }
}
