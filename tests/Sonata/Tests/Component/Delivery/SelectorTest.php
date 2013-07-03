<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\Component\Delivery;

use Sonata\Component\Delivery\Selector;
use Sonata\Component\Delivery\Pool as DeliveryPool;
use Sonata\Component\Product\Pool as ProductPool;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Customer\AddressInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Basket\BasketElementInterface;
use Sonata\Component\Delivery\DeliveryInterface;
use Sonata\ProductBundle\Entity\BaseDelivery;

class Delivery extends \Sonata\Component\Delivery\BaseDelivery
{
    public function isAddressRequired()
    {
        return false;
    }
}

class SelectorTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyBasket()
    {
        $deliveryPool = new DeliveryPool;
        $productPool = new ProductPool;

        $selector = new Selector($deliveryPool, $productPool);
        $this->assertEmpty($selector->getAvailableMethods());
    }

    public function testNoAddress()
    {
        $deliveryPool = new DeliveryPool;
        $productPool = new ProductPool;

        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');
        $selector = new Selector($deliveryPool, $productPool);
        $this->assertEmpty($selector->getAvailableMethods($basket));
    }

    public function testNonExistentProduct()
    {
        $deliveryPool = new DeliveryPool;
        $productPool = new ProductPool;

        $basketElement = $this->getMock('Sonata\Component\Basket\BasketElementInterface');

        $basket  = $this->getMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue(array($basketElement)));

        $address = $this->getMock('Sonata\Component\Customer\AddressInterface');

        $selector = new Selector($deliveryPool, $productPool);
        $this->assertEmpty($selector->getAvailableMethods($basket, $address));
    }

    public function testAvailableMethods()
    {
        $deliveryMethod_low = new Delivery();
        $deliveryMethod_low->setCode('ups_low');
        $deliveryMethod_low->setEnabled(true);
        $deliveryMethod_low->setPriority(1);

        $deliveryMethod_high = new Delivery();
        $deliveryMethod_high->setCode('ups_high');
        $deliveryMethod_high->setEnabled(true);
        $deliveryMethod_high->setPriority(2);

        $deliveryMethod_high_bis = new Delivery();
        $deliveryMethod_high_bis->setCode('ups_high_bis');
        $deliveryMethod_high_bis->setEnabled(true);
        $deliveryMethod_high_bis->setPriority(2);

        $deliveryPool = new DeliveryPool;
        $deliveryPool->addMethod($deliveryMethod_low);
        $deliveryPool->addMethod($deliveryMethod_high);
        $deliveryPool->addMethod($deliveryMethod_high_bis);

        $productPool = new ProductPool;

        $productDelivery_low = $this->getMock('Sonata\Component\Product\DeliveryInterface');
        $productDelivery_low->expects($this->any())->method('getCode')->will($this->returnValue('ups_low'));

        $productDelivery_high = $this->getMock('Sonata\Component\Product\DeliveryInterface');
        $productDelivery_high->expects($this->any())->method('getCode')->will($this->returnValue('ups_high'));

        $productDelivery_high_bis = $this->getMock('Sonata\Component\Product\DeliveryInterface');
        $productDelivery_high_bis->expects($this->any())->method('getCode')->will($this->returnValue('ups_high_bis'));

        $product = $this->getMock('Sonata\Component\Product\ProductInterface');
        $product->expects($this->once())->method('getDeliveries')->will($this->returnValue(array($productDelivery_low, $productDelivery_high, $productDelivery_high_bis)));

        $basketElement = $this->getMock('Sonata\Component\Basket\BasketElementInterface');
        $basketElement->expects($this->once())->method('getProduct')->will($this->returnValue($product));

        $basket  = $this->getMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue(array($basketElement)));

        $address = $this->getMock('Sonata\Component\Customer\AddressInterface');

        $selector = new Selector($deliveryPool, $productPool);
        $selector->setLogger($this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface'));

        $instances = $selector->getAvailableMethods($basket, $address);
        $this->assertCount(3, $instances);
        $this->assertEquals($instances[0]->getCode(), 'ups_high_bis');
        $this->assertEquals($instances[1]->getCode(), 'ups_high');
        $this->assertEquals($instances[2]->getCode(), 'ups_low');
    }
}
