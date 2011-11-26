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

    public function testLoadBasket()
    {

//        $session = $this->getMock('Symfony\Component\HttpFoundation\Session', array('set', 'get'), array(), '', false);
//        $session->expects($this->once())
//            ->method('get')
//            ->will($this->returnValue(null))
//        ;
//
//        $session->expects($this->once())
//            ->method('set')
//        ;
//
//        $productPool = new Pool;
//        $addressManager = $this->getMock('Sonata\Component\Customer\AddressManagerInterface');
//        $deliveryPool = new DeliveryPool;
//        $paymentPool = new PaymentPool;
//        $customerManager = $this->getMock('Sonata\Component\Customer\CustomerManagerInterface');
//
//        $user = $this->getMock('FOS\UserBundle\Model');
//
//        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
//        $token->expects($this->once())
//            ->method('getUser')
//            ->will($this->returnValue($user))
//        ;
//
//        $securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
//        $securityContext->expects($this->once())
//            ->method('getToken')
//            ->will($this->returnValue($token))
//        ;

        $customer         = $this->getMock('Sonata\Component\Customer\CustomerInterface');
        $basket           = $this->getMock('Sonata\Component\Basket\BasketInterface');
        $basketFactory    = $this->getMock('Sonata\Component\Basket\BasketFactoryInterface');
        $basketFactory->expects($this->once())
            ->method('save');
        $basketFactory->expects($this->once())
            ->method('load')
            ->will($this->returnValue($basket));

        $customerSelector = $this->getMock('Sonata\Component\Customer\CustomerSelectorInterface');
        $customerSelector->expects($this->once())
            ->method('get')
            ->will($this->returnValue($customer));

        $loader = new Loader($basketFactory, $customerSelector);

        $this->assertInstanceOf('Sonata\Component\Basket\BasketInterface', $loader->getBasket());
    }

    /**
     * @expectedException        RuntimeException
     */
    public function testExceptionLoadBasket()
    {
        $this->setExpectedException('RuntimeException');

        $customer         = $this->getMock('Sonata\Component\Customer\CustomerInterface');
        $basketFactory    = $this->getMock('Sonata\Component\Basket\BasketFactoryInterface');
        $basketFactory->expects($this->once())
            ->method('load')
            ->will($this->returnCallback(function() {
                throw new \RuntimeException();
            }));

        $customerSelector = $this->getMock('Sonata\Component\Customer\CustomerSelectorInterface');
        $customerSelector->expects($this->once())
            ->method('get')
            ->will($this->returnValue($customer));

        $loader = new Loader($basketFactory, $customerSelector);
        $loader->getBasket();
    }
}