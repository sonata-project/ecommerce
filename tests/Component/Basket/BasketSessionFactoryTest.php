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
use Sonata\Component\Basket\BasketSessionFactory;
use Sonata\Component\Currency\Currency;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class BasketSessionFactoryTest extends TestCase
{
    public function testLoadWithNoBasket(): void
    {
        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('setCustomer');

        $basketManager = $this->createMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('create')->will($this->returnValue($basket));

        $basketBuilder = $this->createMock('Sonata\Component\Basket\BasketBuilderInterface');
        $basketBuilder->expects($this->once())->method('build');

        $customer = $this->createMock('Sonata\Component\Customer\CustomerInterface');
        $customer->expects($this->any())->method('getId')->will($this->returnValue(1));

        $session = $this->createMock('Symfony\Component\HttpFoundation\Session\Session');

        $currencyDetector = $this->createMock('Sonata\Component\Currency\CurrencyDetectorInterface');
        $currency = new Currency();
        $currency->setLabel('EUR');
        $currencyDetector->expects($this->any())
            ->method('getCurrency')
            ->will($this->returnValue($currency))
        ;

        $factory = new BasketSessionFactory($basketManager, $basketBuilder, $currencyDetector, $session);

        $basket = $factory->load($customer);

        $this->isInstanceOf('Sonata\Component\Basket\BasketInterface', $basket);
    }

    public function testLoadWithBasket(): void
    {
        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('setCustomer');

        $basketManager = $this->createMock('Sonata\Component\Basket\BasketManagerInterface');

        $basketBuilder = $this->createMock('Sonata\Component\Basket\BasketBuilderInterface');
        $basketBuilder->expects($this->once())->method('build');

        $customer = $this->createMock('Sonata\Component\Customer\CustomerInterface');
        $customer->expects($this->any())->method('getId')->will($this->returnValue(1));

        $session = new Session(new MockArraySessionStorage());
        $session->set('sonata/basket/factory/customer/1', $basket);

        $currencyDetector = $this->createMock('Sonata\Component\Currency\CurrencyDetectorInterface');
        $currency = new Currency();
        $currency->setLabel('EUR');
        $currencyDetector->expects($this->any())
            ->method('getCurrency')
            ->will($this->returnValue($currency))
        ;

        $factory = new BasketSessionFactory($basketManager, $basketBuilder, $currencyDetector, $session);

        $basket = $factory->load($customer);

        $this->isInstanceOf('Sonata\Component\Basket\BasketInterface', $basket);
    }

    public function testSave(): void
    {
        $basketManager = $this->createMock('Sonata\Component\Basket\BasketManagerInterface');

        $basketBuilder = $this->createMock('Sonata\Component\Basket\BasketBuilderInterface');

        $session = $this->createMock('Symfony\Component\HttpFoundation\Session\SessionInterface');

        $customer = $this->createMock('Sonata\Component\Customer\CustomerInterface');
        $customer->expects($this->any())->method('getId')->will($this->returnValue(1));

        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getCustomer')->will($this->returnValue($customer));

        $currencyDetector = $this->createMock('Sonata\Component\Currency\CurrencyDetectorInterface');
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
        $basketManager = $this->createMock('Sonata\Component\Basket\BasketManagerInterface');

        $basketBuilder = $this->createMock('Sonata\Component\Basket\BasketBuilderInterface');

        $session = $this->createMock('Symfony\Component\HttpFoundation\Session\SessionInterface');
        $session->expects($this->once())->method('remove');

        $currencyDetector = $this->createMock('Sonata\Component\Currency\CurrencyDetectorInterface');

        $factory = new BasketSessionFactory($basketManager, $basketBuilder, $currencyDetector, $session);
        $factory->logout(new Request(), new Response(), $this->createMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface'));
    }
}
