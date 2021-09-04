<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\CustomerBundle\Tests\Controller\Api;

use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use PHPUnit\Framework\TestCase;
use Sonata\Component\Customer\AddressInterface;
use Sonata\Component\Customer\AddressManagerInterface;
use Sonata\Component\Customer\CustomerInterface;
use Sonata\Component\Customer\CustomerManagerInterface;
use Sonata\Component\Order\OrderInterface;
use Sonata\Component\Order\OrderManagerInterface;
use Sonata\CustomerBundle\Controller\Api\CustomerController;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class CustomerControllerTest extends TestCase
{
    public function testGetCustomersAction(): void
    {
        $pager = $this->createStub(PagerInterface::class);
        $customerManager = $this->createMock(CustomerManagerInterface::class);
        $customerManager->expects(static::once())->method('getPager')->willReturn($pager);

        $paramFetcher = $this->createMock(ParamFetcherInterface::class);
        $paramFetcher->expects(static::exactly(3))->method('get')->willReturn(1, 10, null);
        $paramFetcher->expects(static::once())->method('all')->willReturn([]);

        static::assertSame($pager, $this->createCustomerController(null, $customerManager)
            ->getCustomersAction($paramFetcher));
    }

    public function testGetCustomerAction(): void
    {
        $customer = $this->createMock(CustomerInterface::class);
        static::assertSame($customer, $this->createCustomerController($customer)->getCustomerAction(1));
    }

    public function testGetCustomerActionNotFoundException(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Customer (42) not found');

        $this->createCustomerController()->getCustomerAction(42);
    }

    public function testGetCustomerOrdersAction(): void
    {
        $customer = $this->createMock(CustomerInterface::class);
        $order = $this->createMock(OrderInterface::class);

        static::assertSame(
            [$order],
            $this->createCustomerController($customer, null, null, null, $order)->getCustomerOrdersAction(1)
        );
    }

    public function testGetCustomerAddressesAction(): void
    {
        $customer = $this->createMock(CustomerInterface::class);
        $address = $this->createMock(AddressInterface::class);
        $customer->expects(static::once())->method('getAddresses')->willReturn([$address]);

        static::assertSame([$address], $this->createCustomerController($customer)
            ->getCustomerAddressesAction(1));
    }

    public function testPostCustomerAction(): void
    {
        $customer = $this->createMock(CustomerInterface::class);

        $customerManager = $this->createMock(CustomerManagerInterface::class);
        $customerManager->expects(static::once())->method('save')->willReturn($customer);

        $form = $this->createMock(Form::class);
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isSubmitted')->willReturn(true);
        $form->expects(static::once())->method('isValid')->willReturn(true);
        $form->expects(static::once())->method('getData')->willReturn($customer);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createCustomerController(null, $customerManager, null, $formFactory)
            ->postCustomerAction(new Request());

        static::assertInstanceOf(View::class, $view);
    }

    public function testPostCustomerInvalidAction(): void
    {
        $customer = $this->createMock(CustomerInterface::class);

        $customerManager = $this->createMock(CustomerManagerInterface::class);
        $customerManager->expects(static::never())->method('save')->willReturn($customer);

        $form = $this->createMock(Form::class);
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isSubmitted')->willReturn(true);
        $form->expects(static::once())->method('isValid')->willReturn(false);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createCustomerController(null, $customerManager, null, $formFactory)
            ->postCustomerAction(new Request());

        static::assertInstanceOf(FormInterface::class, $view);
    }

    public function testPostCustomerAddressAction(): void
    {
        $customer = $this->createMock(CustomerInterface::class);
        $address = $this->createMock(AddressInterface::class);
        $address->expects(static::once())->method('setCustomer');

        $customerManager = $this->createMock(CustomerManagerInterface::class);

        $addressManager = $this->createMock(AddressManagerInterface::class);
        $addressManager->expects(static::once())->method('save')->willReturn($address);

        $form = $this->createMock(Form::class);
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isSubmitted')->willReturn(true);
        $form->expects(static::once())->method('isValid')->willReturn(true);
        $form->expects(static::once())->method('getData')->willReturn($address);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $customerController = $this->createCustomerController(
            $customer,
            $customerManager,
            $addressManager,
            $formFactory
        );

        $customer = $customerController->postCustomerAddressAction(1, new Request());

        static::assertInstanceOf(AddressInterface::class, $customer);
    }

    public function testPostCustomerAddressInvalidAction(): void
    {
        $customer = $this->createMock(CustomerInterface::class);

        $customerManager = $this->createMock(CustomerManagerInterface::class);
        $customerManager->expects(static::never())->method('save')->willReturn($customer);

        $form = $this->createMock(Form::class);
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isSubmitted')->willReturn(true);
        $form->expects(static::once())->method('isValid')->willReturn(false);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createCustomerController(null, $customerManager, null, $formFactory)
            ->postCustomerAction(new Request());

        static::assertInstanceOf(FormInterface::class, $view);
    }

    public function testPutCustomerAction(): void
    {
        $customer = $this->createMock(CustomerInterface::class);

        $customerManager = $this->createMock(CustomerManagerInterface::class);
        $customerManager->expects(static::once())->method('findOneBy')->willReturn($customer);
        $customerManager->expects(static::once())->method('save')->willReturn($customer);

        $form = $this->createMock(Form::class);
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isSubmitted')->willReturn(true);
        $form->expects(static::once())->method('isValid')->willReturn(true);
        $form->expects(static::once())->method('getData')->willReturn($customer);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createCustomerController($customer, $customerManager, null, $formFactory)
            ->putCustomerAction(1, new Request());

        static::assertInstanceOf(View::class, $view);
    }

    public function testPutCustomerInvalidAction(): void
    {
        $customer = $this->createMock(CustomerInterface::class);

        $customerManager = $this->createMock(CustomerManagerInterface::class);
        $customerManager->expects(static::once())->method('findOneBy')->willReturn($customer);
        $customerManager->expects(static::never())->method('save')->willReturn($customer);

        $form = $this->createMock(Form::class);
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isSubmitted')->willReturn(true);
        $form->expects(static::once())->method('isValid')->willReturn(false);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createCustomerController($customer, $customerManager, null, $formFactory)
            ->putCustomerAction(1, new Request());

        static::assertInstanceOf(FormInterface::class, $view);
    }

    public function testDeleteCustomerAction(): void
    {
        $customer = $this->createMock(CustomerInterface::class);

        $customerManager = $this->createMock(CustomerManagerInterface::class);
        $customerManager->expects(static::once())->method('findOneBy')->willReturn($customer);
        $customerManager->expects(static::once())->method('delete');

        $view = $this->createCustomerController($customer, $customerManager)->deleteCustomerAction(1);

        static::assertSame(['deleted' => true], $view);
    }

    public function testDeleteCustomerInvalidAction(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $customerManager = $this->createMock(CustomerManagerInterface::class);
        $customerManager->expects(static::once())->method('findOneBy')->willReturn(null);
        $customerManager->expects(static::never())->method('delete');

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
            $customerManager = $this->createMock(CustomerManagerInterface::class);
        }
        if (null !== $customer) {
            $customerManager->expects(static::once())->method('findOneBy')->willReturn($customer);
        }
        if (null === $orderManager) {
            $orderManager = $this->createMock(OrderManagerInterface::class);
        }
        if (null === $addressManager) {
            $addressManager = $this->createMock(AddressManagerInterface::class);
        }
        if (null !== $order) {
            $orderManager->expects(static::once())->method('findBy')->willReturn([$order]);
        }
        if (null === $formFactory) {
            $formFactory = $this->createMock(FormFactoryInterface::class);
        }

        return new CustomerController($customerManager, $orderManager, $addressManager, $formFactory);
    }
}
