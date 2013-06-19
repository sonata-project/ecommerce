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
        $customer->expects($this->once())->method('getId')->will($this->returnValue(1));

        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\Session');

        $factory = new BasketEntityFactory($basketManager, $basketBuilder, $session);

        $basket = $factory->load($customer);

        $this->isInstanceOf('Sonata\Component\Basket\BasketInterface', $basket);
    }

    public function testLoadWithBasket()
    {
        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('setCustomer');

        $basketManager = $this->getMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('loadBasketPerCustomer')->will($this->returnValue($basket));

        $basketBuilder = $this->getMock('Sonata\Component\Basket\BasketBuilderInterface');
        $basketBuilder->expects($this->once())->method('build');

        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');
        $customer->expects($this->once())->method('getId')->will($this->returnValue(1));

        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\Session');

        $factory = new BasketEntityFactory($basketManager, $basketBuilder, $session);

        $basket = $factory->load($customer);

        $this->isInstanceOf('Sonata\Component\Basket\BasketInterface', $basket);
    }

    public function testSaveExistingCustomer()
    {
        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getCustomerId')->will($this->returnValue(1));

        $basketManager = $this->getMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('save');

        $basketBuilder = $this->getMock('Sonata\Component\Basket\BasketBuilderInterface');

        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\Session');

        $factory = new BasketEntityFactory($basketManager, $basketBuilder, $session);
        $factory->save($basket);
    }

    public function testSaveNoExistingCustomer()
    {
        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getCustomerId')->will($this->returnValue(false));

        $basketManager = $this->getMock('Sonata\Component\Basket\BasketManagerInterface');

        $basketBuilder = $this->getMock('Sonata\Component\Basket\BasketBuilderInterface');

        $session = new Session();

        $factory = new BasketEntityFactory($basketManager, $basketBuilder, $session);
        $factory->save($basket);

        $this->assertEquals($basket, $session->get('sonata/basket/factory/customer/new'));
    }
}
