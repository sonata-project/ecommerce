<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\Component\Basket;

use Sonata\Component\Basket\BasketManagerInterface;
use Sonata\Component\Basket\BasketBuilderInterface;
use Sonata\Component\Basket\BasketEntityFactory;
use Sonata\Component\Customer\CustomerInterface;
use Sonata\Component\Basket\BasketInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Sonata\Component\Currency\Currency;

class BasketEntityFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadWithNoBasket()
    {
        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('setCustomer');

        $basketManager = $this->getMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('loadBasketPerCustomer')->will($this->returnValue(false));
        $basketManager->expects($this->once())->method('create')->will($this->returnValue($basket));

        $basketBuilder = $this->getMock('Sonata\Component\Basket\BasketBuilderInterface');
        $basketBuilder->expects($this->once())->method('build');

        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');
        $customer->expects($this->exactly(3))->method('getId')->will($this->returnValue(1));

        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\Session');

        $currencyDetector = $this->getMock('Sonata\Component\Currency\CurrencyDetectorInterface');
        $currency = new Currency();
        $currency->setLabel("EUR");
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
        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('setCustomer');

        $basketManager = $this->getMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('loadBasketPerCustomer')->will($this->returnValue(false));

        $basketBuilder = $this->getMock('Sonata\Component\Basket\BasketBuilderInterface');

        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');
        $customer->expects($this->exactly(3))->method('getId')->will($this->returnValue(1));

        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\Session');
        $session->expects($this->exactly(1))->method('get')->will($this->returnValue($basket));

        $currencyDetector = $this->getMock('Sonata\Component\Currency\CurrencyDetectorInterface');
        $currency = new Currency();
        $currency->setLabel("EUR");
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
        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');

        $sessionBasket = $this->getMock('Sonata\Component\Basket\BasketInterface');

        $basketManager = $this->getMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('loadBasketPerCustomer')->will($this->returnValue($basket));

        $basketBuilder = $this->getMock('Sonata\Component\Basket\BasketBuilderInterface');
        $basketBuilder->expects($this->exactly(2))->method('build');

        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');
        $customer->expects($this->exactly(5))->method('getId')->will($this->returnValue(1));

        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\Session');
        $session->expects($this->exactly(1))->method('get')->will($this->returnValue($sessionBasket));

        $currencyDetector = $this->getMock('Sonata\Component\Currency\CurrencyDetectorInterface');
        $currency = new Currency();
        $currency->setLabel("EUR");
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
        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getCustomerId')->will($this->returnValue(1));

        $basketManager = $this->getMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('save');

        $basketBuilder = $this->getMock('Sonata\Component\Basket\BasketBuilderInterface');

        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\Session');

        $currencyDetector = $this->getMock('Sonata\Component\Currency\CurrencyDetectorInterface');
        $currency = new Currency();
        $currency->setLabel("EUR");
        $currencyDetector->expects($this->any())
            ->method('getCurrency')
            ->will($this->returnValue($currency))
        ;

        $factory = new BasketEntityFactory($basketManager, $basketBuilder, $currencyDetector, $session);
        $factory->save($basket);
    }

    public function testSaveNoExistingCustomer()
    {
        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getCustomerId')->will($this->returnValue(false));

        $basketManager = $this->getMock('Sonata\Component\Basket\BasketManagerInterface');

        $basketBuilder = $this->getMock('Sonata\Component\Basket\BasketBuilderInterface');

        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\SessionInterface');
        $tester = $this;
        $session->expects($this->once())->method('set')->will($this->returnCallback(function ($key, $value) use ($tester, $basket) {
            $tester->assertEquals($basket, $value);
            $tester->assertEquals('sonata/basket/factory/customer/new', $key);
        }));

        $currencyDetector = $this->getMock('Sonata\Component\Currency\CurrencyDetectorInterface');
        $currency = new Currency();
        $currency->setLabel("EUR");
        $currencyDetector->expects($this->any())
            ->method('getCurrency')
            ->will($this->returnValue($currency))
        ;

        $factory = new BasketEntityFactory($basketManager, $basketBuilder, $currencyDetector, $session);
        $factory->save($basket);
    }

    public function testReset()
    {
        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getCustomerId')->will($this->returnValue(1));

        $basketManager = $this->getMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('delete');

        $basketBuilder = $this->getMock('Sonata\Component\Basket\BasketBuilderInterface');

        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\Session');

        $currencyDetector = $this->getMock('Sonata\Component\Currency\CurrencyDetectorInterface');
        $currency = new Currency();
        $currency->setLabel("EUR");
        $currencyDetector->expects($this->any())
            ->method('getCurrency')
            ->will($this->returnValue($currency))
        ;

        $factory = new BasketEntityFactory($basketManager, $basketBuilder, $currencyDetector, $session);
        $factory->reset($basket);
    }
}
