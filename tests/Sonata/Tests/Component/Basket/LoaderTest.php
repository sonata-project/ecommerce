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

use Sonata\Component\Basket\Basket;
use Sonata\Component\Basket\BasketElement;
use Sonata\Component\Product\Pool;
use Sonata\Tests\Component\Basket\Delivery;
use Sonata\Tests\Component\Basket\Payment;

use Sonata\Component\Customer\AddressManagerInterface;
use Sonata\Component\Customer\CustomerManagerInterface;
use Sonata\Component\Delivery\Pool as DeliveryPool;
use Sonata\Component\Payment\Pool as PaymentPool;

use Sonata\Component\Basket\Loader;

class LoaderTest extends \PHPUnit_Framework_TestCase
{

    public function testSession()
    {

        $session = $this->getMock('Symfony\Component\HttpFoundation\Session', array('set', 'get'), array(), '', false);
        $session->expects($this->once())
            ->method('get')
            ->will($this->returnValue(null))
        ;

        $session->expects($this->once())
            ->method('set')
        ;

        $productPool = new Pool;
        $addressManager = $this->getMock('Sonata\Component\Customer\AddressManagerInterface');
        $deliveryPool = new DeliveryPool;
        $paymentPool = new PaymentPool;
        $customerManager = $this->getMock('Sonata\Component\Customer\CustomerManagerInterface');

        $loader = new Loader(
            'Sonata\\Component\\Basket\\Basket',
            $session,
            $productPool,
            $addressManager,
            $deliveryPool,
            $paymentPool,
            $customerManager
        );

        $basket = $loader->getBasket();

        $this->assertInstanceOf('Sonata\\Component\\Basket\\Basket', $basket);
    }
}