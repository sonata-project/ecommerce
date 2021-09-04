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
use Sonata\Component\Basket\BasketEntityFactory;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Basket\BasketManagerInterface;
use Sonata\Component\Currency\Currency;
use Sonata\Component\Currency\CurrencyDetectorInterface;
use Sonata\Component\Customer\CustomerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class BasketEntityFactoryTest extends TestCase
{
    public function testLoadWithNoBasket(): void
    {
        $basket = $this->createMock(BasketInterface::class);
        $basket->expects(static::once())->method('setCustomer');

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects(static::once())->method('loadBasketPerCustomer')->willReturn(false);
        $basketManager->expects(static::once())->method('create')->willReturn($basket);

        $basketBuilder = $this->createMock(BasketBuilderInterface::class);
        $basketBuilder->expects(static::once())->method('build');

        $customer = $this->createMock(CustomerInterface::class);
        $customer->expects(static::exactly(3))->method('getId')->willReturn(1);

        $session = $this->createMock(Session::class);

        $currencyDetector = $this->createMock(CurrencyDetectorInterface::class);
        $currency = new Currency();
        $currency->setLabel('EUR');
        $currencyDetector->expects(static::any())
            ->method('getCurrency')
            ->willReturn($currency);

        $factory = new BasketEntityFactory($basketManager, $basketBuilder, $currencyDetector, $session);

        $basket = $factory->load($customer);

        static::isInstanceOf(BasketInterface::class, $basket);
    }

    public function testLoadWithNoBasketInDbButBasketInSession(): void
    {
        $basket = $this->createMock(BasketInterface::class);
        $basket->expects(static::once())->method('setCustomer');

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects(static::once())->method('loadBasketPerCustomer')->willReturn(false);

        $basketBuilder = $this->createMock(BasketBuilderInterface::class);

        $customer = $this->createMock(CustomerInterface::class);
        $customer->expects(static::exactly(3))->method('getId')->willReturn(1);

        $session = $this->createMock(Session::class);
        $session->expects(static::exactly(1))->method('get')->willReturn($basket);

        $currencyDetector = $this->createMock(CurrencyDetectorInterface::class);
        $currency = new Currency();
        $currency->setLabel('EUR');
        $currencyDetector->expects(static::any())
            ->method('getCurrency')
            ->willReturn($currency);

        $factory = new BasketEntityFactory($basketManager, $basketBuilder, $currencyDetector, $session);

        $basket = $factory->load($customer);

        static::isInstanceOf(BasketInterface::class, $basket);
    }

    public function testLoadWithBasketInDbAndInSession(): void
    {
        $basket = $this->createMock(BasketInterface::class);

        $sessionBasket = $this->createMock(BasketInterface::class);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects(static::once())->method('loadBasketPerCustomer')->willReturn($basket);

        $basketBuilder = $this->createMock(BasketBuilderInterface::class);
        $basketBuilder->expects(static::exactly(2))->method('build');

        $customer = $this->createMock(CustomerInterface::class);
        $customer->expects(static::exactly(5))->method('getId')->willReturn(1);

        $session = $this->createMock(Session::class);
        $session->expects(static::exactly(1))->method('get')->willReturn($sessionBasket);

        $currencyDetector = $this->createMock(CurrencyDetectorInterface::class);
        $currency = new Currency();
        $currency->setLabel('EUR');
        $currencyDetector->expects(static::any())
            ->method('getCurrency')
            ->willReturn($currency);

        $factory = new BasketEntityFactory($basketManager, $basketBuilder, $currencyDetector, $session);

        $loadedBasket = $factory->load($customer);

        static::isInstanceOf(BasketInterface::class, $loadedBasket);
    }

    public function testSaveExistingCustomer(): void
    {
        $basket = $this->createMock(BasketInterface::class);
        $basket->expects(static::once())->method('getCustomerId')->willReturn(1);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects(static::once())->method('save');

        $basketBuilder = $this->createMock(BasketBuilderInterface::class);

        $session = $this->createMock(Session::class);

        $currencyDetector = $this->createMock(CurrencyDetectorInterface::class);
        $currency = new Currency();
        $currency->setLabel('EUR');
        $currencyDetector->expects(static::any())
            ->method('getCurrency')
            ->willReturn($currency);

        $factory = new BasketEntityFactory($basketManager, $basketBuilder, $currencyDetector, $session);
        $factory->save($basket);
    }

    public function testSaveNoExistingCustomer(): void
    {
        $basket = $this->createMock(BasketInterface::class);
        $basket->expects(static::once())->method('getCustomerId')->willReturn(false);

        $basketManager = $this->createMock(BasketManagerInterface::class);

        $basketBuilder = $this->createMock(BasketBuilderInterface::class);

        $session = $this->createMock(SessionInterface::class);
        $tester = $this;
        $session->expects(static::once())->method('set')->willReturnCallback(static function ($key, $value) use ($tester, $basket): void {
            $tester->assertEquals($basket, $value);
            $tester->assertEquals('sonata/basket/factory/customer/new', $key);
        });

        $currencyDetector = $this->createMock(CurrencyDetectorInterface::class);
        $currency = new Currency();
        $currency->setLabel('EUR');
        $currencyDetector->expects(static::any())
            ->method('getCurrency')
            ->willReturn($currency);

        $factory = new BasketEntityFactory($basketManager, $basketBuilder, $currencyDetector, $session);
        $factory->save($basket);
    }

    public function testReset(): void
    {
        $basket = $this->createMock(BasketInterface::class);
        $basket->expects(static::once())->method('getCustomerId')->willReturn(1);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects(static::once())->method('delete');

        $basketBuilder = $this->createMock(BasketBuilderInterface::class);

        $session = $this->createMock(Session::class);

        $currencyDetector = $this->createMock(CurrencyDetectorInterface::class);
        $currency = new Currency();
        $currency->setLabel('EUR');
        $currencyDetector->expects(static::any())
            ->method('getCurrency')
            ->willReturn($currency);

        $factory = new BasketEntityFactory($basketManager, $basketBuilder, $currencyDetector, $session);
        $factory->reset($basket);
    }
}
