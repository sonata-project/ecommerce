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

namespace Sonata\Component\Tests\Basket;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Basket\BasketBuilderInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Basket\BasketManagerInterface;
use Sonata\Component\Basket\BasketSessionFactory;
use Sonata\Component\Currency\Currency;
use Sonata\Component\Currency\CurrencyDetectorInterface;
use Sonata\Component\Customer\CustomerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class BasketSessionFactoryTest extends TestCase
{
    public function testLoadWithNoBasket(): void
    {
        $basket = $this->createMock(BasketInterface::class);
        $basket->expects($this->once())->method('setCustomer');

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->once())->method('create')->will($this->returnValue($basket));

        $basketBuilder = $this->createMock(BasketBuilderInterface::class);
        $basketBuilder->expects($this->once())->method('build');

        $customer = $this->createMock(CustomerInterface::class);
        $customer->expects($this->any())->method('getId')->will($this->returnValue(1));

        $session = $this->createMock(Session::class);

        $currencyDetector = $this->createMock(CurrencyDetectorInterface::class);
        $currency = new Currency();
        $currency->setLabel('EUR');
        $currencyDetector->expects($this->any())
            ->method('getCurrency')
            ->will($this->returnValue($currency))
        ;

        $factory = new BasketSessionFactory($basketManager, $basketBuilder, $currencyDetector, $session);

        $basket = $factory->load($customer);

        $this->isInstanceOf(BasketInterface::class, $basket);
    }

    public function testLoadWithBasket(): void
    {
        $basket = $this->createMock(BasketInterface::class);
        $basket->expects($this->once())->method('setCustomer');

        $basketManager = $this->createMock(BasketManagerInterface::class);

        $basketBuilder = $this->createMock(BasketBuilderInterface::class);
        $basketBuilder->expects($this->once())->method('build');

        $customer = $this->createMock(CustomerInterface::class);
        $customer->expects($this->any())->method('getId')->will($this->returnValue(1));

        $session = new Session(new MockArraySessionStorage());
        $session->set('sonata/basket/factory/customer/1', $basket);

        $currencyDetector = $this->createMock(CurrencyDetectorInterface::class);
        $currency = new Currency();
        $currency->setLabel('EUR');
        $currencyDetector->expects($this->any())
            ->method('getCurrency')
            ->will($this->returnValue($currency))
        ;

        $factory = new BasketSessionFactory($basketManager, $basketBuilder, $currencyDetector, $session);

        $basket = $factory->load($customer);

        $this->isInstanceOf(BasketInterface::class, $basket);
    }

    public function testSave(): void
    {
        $basketManager = $this->createMock(BasketManagerInterface::class);

        $basketBuilder = $this->createMock(BasketBuilderInterface::class);

        $session = $this->createMock(SessionInterface::class);

        $customer = $this->createMock(CustomerInterface::class);
        $customer->expects($this->any())->method('getId')->will($this->returnValue(1));

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects($this->once())->method('getCustomer')->will($this->returnValue($customer));

        $currencyDetector = $this->createMock(CurrencyDetectorInterface::class);
        $currency = new Currency();
        $currency->setLabel('EUR');
        $currencyDetector->expects($this->any())
            ->method('getCurrency')
            ->will($this->returnValue($currency))
        ;

        $factory = new BasketSessionFactory($basketManager, $basketBuilder, $currencyDetector, $session);
        $factory->save($basket);
    }

    public function testLogout(): void
    {
        $basketManager = $this->createMock(BasketManagerInterface::class);

        $basketBuilder = $this->createMock(BasketBuilderInterface::class);

        $session = $this->createMock(SessionInterface::class);
        $session->expects($this->once())->method('remove');

        $currencyDetector = $this->createMock(CurrencyDetectorInterface::class);

        $factory = new BasketSessionFactory($basketManager, $basketBuilder, $currencyDetector, $session);
        $factory->logout(new Request(), new Response(), $this->createMock(TokenInterface::class));
    }
}
