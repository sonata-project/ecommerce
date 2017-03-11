<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Tests\Basket;

use Sonata\Component\Basket\BasketEntityFactory;
use Sonata\Component\Currency\Currency;
use Sonata\Tests\Helpers\PHPUnit_Framework_TestCase;

class BasketEntityFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testLoadWithNoBasket()
    {
        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('setCustomer');

        $basketManager = $this->createMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('loadBasketPerCustomer')->will($this->returnValue(false));
        $basketManager->expects($this->once())->method('create')->will($this->returnValue($basket));

        $basketBuilder = $this->createMock('Sonata\Component\Basket\BasketBuilderInterface');
        $basketBuilder->expects($this->once())->method('build');

        $customer = $this->createMock('Sonata\Component\Customer\CustomerInterface');
        $customer->expects($this->exactly(3))->method('getId')->will($this->returnValue(1));

        $session = $this->createMock('Symfony\Component\HttpFoundation\Session\Session');

        $currencyDetector = $this->createMock('Sonata\Component\Currency\CurrencyDetectorInterface');
        $currency = new Currency();
        $currency->setLabel('EUR');
        $currencyDetector->expects($this->any())
            ->method('getCurrency')
            ->will($this->returnValue($currency))
        ;

        $factory = new BasketEntityFactory($basketManager, $basketBuilder, $currencyDetector, $session);

        $basket = $factory->load($customer);

        $this->isInstanceOf('Sonata\Component\Basket\BasketInterface', $basket);
    }

    public function testLoadWithNoBasketInDbButBasketInSession()
    {
        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('setCustomer');

        $basketManager = $this->createMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('loadBasketPerCustomer')->will($this->returnValue(false));

        $basketBuilder = $this->createMock('Sonata\Component\Basket\BasketBuilderInterface');

        $customer = $this->createMock('Sonata\Component\Customer\CustomerInterface');
        $customer->expects($this->exactly(3))->method('getId')->will($this->returnValue(1));

        $session = $this->createMock('Symfony\Component\HttpFoundation\Session\Session');
        $session->expects($this->exactly(1))->method('get')->will($this->returnValue($basket));

        $currencyDetector = $this->createMock('Sonata\Component\Currency\CurrencyDetectorInterface');
        $currency = new Currency();
        $currency->setLabel('EUR');
        $currencyDetector->expects($this->any())
            ->method('getCurrency')
            ->will($this->returnValue($currency))
        ;

        $factory = new BasketEntityFactory($basketManager, $basketBuilder, $currencyDetector, $session);

        $basket = $factory->load($customer);

        $this->isInstanceOf('Sonata\Component\Basket\BasketInterface', $basket);
    }

    public function testLoadWithBasketInDbAndInSession()
    {
        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');

        $sessionBasket = $this->createMock('Sonata\Component\Basket\BasketInterface');

        $basketManager = $this->createMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('loadBasketPerCustomer')->will($this->returnValue($basket));

        $basketBuilder = $this->createMock('Sonata\Component\Basket\BasketBuilderInterface');
        $basketBuilder->expects($this->exactly(2))->method('build');

        $customer = $this->createMock('Sonata\Component\Customer\CustomerInterface');
        $customer->expects($this->exactly(5))->method('getId')->will($this->returnValue(1));

        $session = $this->createMock('Symfony\Component\HttpFoundation\Session\Session');
        $session->expects($this->exactly(1))->method('get')->will($this->returnValue($sessionBasket));

        $currencyDetector = $this->createMock('Sonata\Component\Currency\CurrencyDetectorInterface');
        $currency = new Currency();
        $currency->setLabel('EUR');
        $currencyDetector->expects($this->any())
            ->method('getCurrency')
            ->will($this->returnValue($currency))
        ;

        $factory = new BasketEntityFactory($basketManager, $basketBuilder, $currencyDetector, $session);

        $loadedBasket = $factory->load($customer);

        $this->isInstanceOf('Sonata\Component\Basket\BasketInterface', $loadedBasket);
    }

    public function testSaveExistingCustomer()
    {
        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getCustomerId')->will($this->returnValue(1));

        $basketManager = $this->createMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('save');

        $basketBuilder = $this->createMock('Sonata\Component\Basket\BasketBuilderInterface');

        $session = $this->createMock('Symfony\Component\HttpFoundation\Session\Session');

        $currencyDetector = $this->createMock('Sonata\Component\Currency\CurrencyDetectorInterface');
        $currency = new Currency();
        $currency->setLabel('EUR');
        $currencyDetector->expects($this->any())
            ->method('getCurrency')
            ->will($this->returnValue($currency))
        ;

        $factory = new BasketEntityFactory($basketManager, $basketBuilder, $currencyDetector, $session);
        $factory->save($basket);
    }

    public function testSaveNoExistingCustomer()
    {
        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getCustomerId')->will($this->returnValue(false));

        $basketManager = $this->createMock('Sonata\Component\Basket\BasketManagerInterface');

        $basketBuilder = $this->createMock('Sonata\Component\Basket\BasketBuilderInterface');

        $session = $this->createMock('Symfony\Component\HttpFoundation\Session\SessionInterface');
        $tester = $this;
        $session->expects($this->once())->method('set')->will($this->returnCallback(function ($key, $value) use ($tester, $basket) {
            $tester->assertEquals($basket, $value);
            $tester->assertEquals('sonata/basket/factory/customer/new', $key);
        }));

        $currencyDetector = $this->createMock('Sonata\Component\Currency\CurrencyDetectorInterface');
        $currency = new Currency();
        $currency->setLabel('EUR');
        $currencyDetector->expects($this->any())
            ->method('getCurrency')
            ->will($this->returnValue($currency))
        ;

        $factory = new BasketEntityFactory($basketManager, $basketBuilder, $currencyDetector, $session);
        $factory->save($basket);
    }

    public function testReset()
    {
        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getCustomerId')->will($this->returnValue(1));

        $basketManager = $this->createMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('delete');

        $basketBuilder = $this->createMock('Sonata\Component\Basket\BasketBuilderInterface');

        $session = $this->createMock('Symfony\Component\HttpFoundation\Session\Session');

        $currencyDetector = $this->createMock('Sonata\Component\Currency\CurrencyDetectorInterface');
        $currency = new Currency();
        $currency->setLabel('EUR');
        $currencyDetector->expects($this->any())
            ->method('getCurrency')
            ->will($this->returnValue($currency))
        ;

        $factory = new BasketEntityFactory($basketManager, $basketBuilder, $currencyDetector, $session);
        $factory->reset($basket);
    }
}
