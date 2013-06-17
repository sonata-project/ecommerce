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
use Sonata\Component\Basket\BasketSessionFactory;
use Sonata\Component\Customer\CustomerInterface;
use Sonata\Component\Basket\BasketInterface;
use Symfony\Component\HttpFoundation\SessionStorage\SessionStorageInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class BasketSessionFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadWithNoBasket()
    {
        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('setCustomer');

        $basketManager = $this->getMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('create')->will($this->returnValue($basket));

        $basketBuilder = $this->getMock('Sonata\Component\Basket\BasketBuilderInterface');
        $basketBuilder->expects($this->once())->method('build');

        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');
        $customer->expects($this->once())->method('getId')->will($this->returnValue(1));

        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\Session');

        $factory = new BasketSessionFactory($basketManager, $basketBuilder, $session);

        $basket = $factory->load($customer);

        $this->isInstanceOf('Sonata\Component\Basket\BasketInterface', $basket);
    }

    public function testLoadWithBasket()
    {
        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('setCustomer');

        $basketManager = $this->getMock('Sonata\Component\Basket\BasketManagerInterface');

        $basketBuilder = $this->getMock('Sonata\Component\Basket\BasketBuilderInterface');
        $basketBuilder->expects($this->once())->method('build');

        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');
        $customer->expects($this->once())->method('getId')->will($this->returnValue(1));

        $session = new Session();
        $session->set('sonata/basket/factory/customer/1', $basket);

        $factory = new BasketSessionFactory($basketManager, $basketBuilder, $session);

        $basket = $factory->load($customer);

        $this->isInstanceOf('Sonata\Component\Basket\BasketInterface', $basket);
    }

    public function testSave()
    {
        $basketManager = $this->getMock('Sonata\Component\Basket\BasketManagerInterface');

        $basketBuilder = $this->getMock('Sonata\Component\Basket\BasketBuilderInterface');

        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\Session');

        $customer = $this->getMock('Sonata\Component\Customer\CustomerInterface');
        $customer->expects($this->once())->method('getId')->will($this->returnValue(1));

        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getCustomer')->will($this->returnValue($customer));

        $factory = new BasketSessionFactory($basketManager, $basketBuilder, $session);
        $factory->save($basket);
    }
}
