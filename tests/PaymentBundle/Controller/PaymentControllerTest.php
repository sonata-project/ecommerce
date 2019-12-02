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

namespace Sonata\PaymentBundle\Tests\Controller;

use Buzz\Browser;
use Buzz\Client\ClientInterface;
use PHPUnit\Framework\TestCase;
use Sonata\Component\Basket\Basket;
use Sonata\Component\Basket\BasketFactoryInterface;
use Sonata\Component\Payment\Debug\DebugPayment;
use Sonata\Component\Payment\PaymentHandlerInterface;
use Sonata\Component\Tests\Payment\DebugPaymentTest_Order;
use Sonata\OrderBundle\Entity\BaseOrderElement;
use Sonata\PaymentBundle\Controller\PaymentController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

final class PaymentControllerTest extends TestCase
{
    public function testInstanceBasketInterfacePaymentAction(): void
    {
        $classPaymentController = new \ReflectionClass(PaymentController::class);
        $methodGetBasketFactory = $classPaymentController->getMethod('getBasketFactory');
        $methodGetBasketFactory->setAccessible(true);

        $methodGetBasket = $classPaymentController->getMethod('getBasket');
        $methodGetBasket->setAccessible(true);

        $methodGetPaymentHandler = $classPaymentController->getMethod('getPaymentHandler');
        $methodGetPaymentHandler->setAccessible(true);

        $basketFactoryInterface = $this->createMock(BasketFactoryInterface::class);
        $paymentHandlerInterface = $this->createMock(PaymentHandlerInterface::class);
        $basket = $this->createMock(Basket::class);

        $paymentController = $this->createPaymentController($basketFactoryInterface, $paymentHandlerInterface, $basket);

        $this->assertSame($basketFactoryInterface, $methodGetBasketFactory->invoke($paymentController));
        $this->assertSame($paymentHandlerInterface, $methodGetPaymentHandler->invoke($paymentController));
        $this->assertSame($basket, $methodGetBasket->invoke($paymentController));
    }

    /**
     * @group legacy
     */
    public function testInstanceNullBasketInterfacePaymentAction(): void
    {
        $classPaymentController = new \ReflectionClass(PaymentController::class);
        $methodGetBasketFactory = $classPaymentController->getMethod('getBasketFactory');
        $methodGetBasketFactory->setAccessible(true);

        $methodGetBasket = $classPaymentController->getMethod('getBasket');
        $methodGetBasket->setAccessible(true);

        $methodGetPaymentHandler = $classPaymentController->getMethod('getPaymentHandler');
        $methodGetPaymentHandler->setAccessible(true);

        $basketFactoryInterface = $this->createMock(BasketFactoryInterface::class);
        $basket = $this->createMock(Basket::class);
        $paymentHandlerInterface = $this->createMock(PaymentHandlerInterface::class);

        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->at(0))
            ->method('get')
            ->with('sonata.basket.factory')
            ->willReturn($basketFactoryInterface);

        $container->expects($this->at(1))
            ->method('get')
            ->with('sonata.basket')
            ->willReturn($basket);

        $container->expects($this->at(2))
            ->method('get')
            ->with('sonata.payment.handler')
            ->willReturn($paymentHandlerInterface);

        $paymentController = new PaymentController();

        $paymentController->setContainer($container);

        $this->assertSame($basketFactoryInterface, $methodGetBasketFactory->invoke($paymentController));
        $this->assertSame($basket, $methodGetBasket->invoke($paymentController));
        $this->assertSame($paymentHandlerInterface, $methodGetPaymentHandler->invoke($paymentController));
    }

    public function testSendBankAction(): void
    {
        $request = new Request();
        $request->setMethod('POST');

        $payment = $this->getDebugPayment();

        $order = $this->getOrder();

        $paymentHandlerInterface = $this->createMock(PaymentHandlerInterface::class);
        $paymentHandlerInterface->expects($this->once())
            ->method('getSendbankOrder')
            ->willReturn($order);

        $basket = $this->createMock(Basket::class);
        $basket->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $basket->expects($this->once())
            ->method('getPaymentMethod')
            ->willReturn($payment);

        $paymentController = $this->createPaymentController(null, $paymentHandlerInterface, $basket);

        $this->assertInstanceOf(Response::class, $paymentController->sendbankAction($request));
    }

    private function getOrder(): DebugPaymentTest_Order
    {
        $date = new \DateTime('1981-11-30', new \DateTimeZone('Europe/Paris'));

        $order = new DebugPaymentTest_Order();
        $order->setCreatedAt($date);

        $element1 = $this->getMockBuilder(BaseOrderElement::class)->getMock();
        $element1->expects($this->any())->method('getVatRate')->willReturn(20);
        $element1->expects($this->any())->method('getVatAmount')->willReturn(3);

        $element2 = $this->getMockBuilder(BaseOrderElement::class)->getMock();
        $element2->expects($this->any())->method('getVatRate')->willReturn(10);
        $element2->expects($this->any())->method('getVatAmount')->willReturn(2);

        $order->setOrderElements([$element1, $element2]);

        $order->setReference('my-reference');

        return $order;
    }

    private function getDebugPayment(): DebugPayment
    {
        $router = $this->createMock(RouterInterface::class);
        $router->expects($this->exactly(1))->method('generate')->willReturn('http://foo.bar/ok-url');

        $client = $this->createMock(ClientInterface::class);

        $browser = new Browser($client);

        $payment = new DebugPayment($router, $browser);

        return $payment;
    }

    private function createPaymentController(
        ?BasketFactoryInterface $basketFactory = null,
        ?PaymentHandlerInterface $paymentHandler = null,
        ?Basket $basket = null
    ): PaymentController {
        if (null === $basketFactory) {
            $basketFactory = $this->createMock(BasketFactoryInterface::class);
        }

        if (null === $paymentHandler) {
            $paymentHandler = $this->createMock(PaymentHandlerInterface::class);
        }

        if (null === $basket) {
            $basket = $this->createMock(Basket::class);
        }

        return new PaymentController($basketFactory, $paymentHandler, $basket);
    }
}
