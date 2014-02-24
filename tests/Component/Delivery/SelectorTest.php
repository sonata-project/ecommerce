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
use Sonata\ProductBundle\Entity\BaseProduct;

class SelectorTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyBasket()
    {
        $deliveryPool = new DeliveryPool;
        $productPool = new ProductPool;

        $selector = new Selector($deliveryPool, $productPool);
        $this->assertEmpty($selector->getAvailableMethods());
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

    /**
     * Test the getAvailableMethods methods with no basket nor address provided
     */
    public function testGetAvailableMethodsWithoutBasket()
    {
        $deliveryPool = new DeliveryPool;
        $productPool = new ProductPool;

        $selector = new Selector($deliveryPool, $productPool);
        $this->assertEmpty($selector->getAvailableMethods());
    }

    /**
     * Test the getAvailableMethods methods with an empty basket provided
     */
    public function testGetAvailableMethodsWithEmptyBasket()
    {
        $deliveryPool = new DeliveryPool;
        $productPool = new ProductPool;
        $basket  = $this->getMock('Sonata\Component\Basket\Basket');
        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue(array()));

        $selector = new Selector($deliveryPool, $productPool);
        $this->assertEmpty($selector->getAvailableMethods($basket));
    }

    /**
     * Test the getAvailableMethods methods with a product provided but no address and no related delivery methods
     */
    public function testGetAvailableMethodsWithFilledBasket()
    {
        $deliveryPool = new DeliveryPool;
        $productPool = new ProductPool;

        $basket  = $this->getMock('Sonata\Component\Basket\Basket');
        $basketElement = $this->getMock('Sonata\Component\Basket\BasketElement');
        $product = $this->getMock('Sonata\Tests\Component\Delivery\Product');

        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue(array($basketElement)));
        $basketElement->expects($this->once())->method('getProduct')->will($this->returnValue($product));
        $product->expects($this->once())->method('getDeliveries')->will($this->returnValue(array()));

        $selector = new Selector($deliveryPool, $productPool);
        $this->assertEmpty($selector->getAvailableMethods($basket));
    }

    /**
     * Provide a delivery method that require an address but none is provided
     */
    public function testGetAvailableMethodsWithRequiredAddressDelivery()
    {
        $deliveryPool = $this->getMock('Sonata\Component\Delivery\Pool');
        $productPool = new ProductPool;

        $basket  = $this->getMock('Sonata\Component\Basket\Basket');
        $basketElement = $this->getMock('Sonata\Component\Basket\BasketElement');
        $product = $this->getMock('Sonata\Tests\Component\Delivery\Product');

        $delivery = $this->getMock('Sonata\Component\Product\DeliveryInterface');
        $delivery->expects($this->any())->method('getCode')->will($this->returnValue('deliveryTest'));

        $serviceDelivery = $this->getMock('Sonata\Component\Delivery\BaseServiceDelivery');
        $serviceDelivery->expects($this->any())->method('getCode')->will($this->returnValue('deliveryTest'));
        $serviceDelivery->expects($this->any())->method('getEnabled')->will($this->returnValue(true));
        $serviceDelivery->expects($this->any())->method('isAddressRequired')->will($this->returnValue(true));
        $deliveryPool->expects($this->any())->method('getMethod')->will($this->returnValue($serviceDelivery));

        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue(array($basketElement)));
        $basketElement->expects($this->once())->method('getProduct')->will($this->returnValue($product));
        $product->expects($this->once())->method('getDeliveries')->will($this->returnValue(array($delivery)));

        $selector = new Selector($deliveryPool, $productPool);
        $this->assertEquals(array(), $selector->getAvailableMethods($basket));
    }

    /**
     * Provide a delivery method that require an address but none is provided
     */
    public function testGetAvailableMethodsWithDisabledDelivery()
    {
        $deliveryPool = $this->getMock('Sonata\Component\Delivery\Pool');
        $productPool = new ProductPool;

        $basket  = $this->getMock('Sonata\Component\Basket\Basket');
        $basketElement = $this->getMock('Sonata\Component\Basket\BasketElement');
        $product = $this->getMock('Sonata\Tests\Component\Delivery\Product');

        $delivery = $this->getMock('Sonata\Component\Product\DeliveryInterface');
        $delivery->expects($this->any())->method('getCode')->will($this->returnValue('deliveryTest'));

        $serviceDelivery = $this->getMock('Sonata\Component\Delivery\BaseServiceDelivery');
        $serviceDelivery->expects($this->any())->method('getCode')->will($this->returnValue('deliveryTest'));
        $deliveryPool->expects($this->any())->method('getMethod')->will($this->returnValue($serviceDelivery));
        $serviceDelivery->expects($this->any())->method('getEnabled')->will($this->returnValue(false));

        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue(array($basketElement)));
        $basketElement->expects($this->once())->method('getProduct')->will($this->returnValue($product));
        $product->expects($this->once())->method('getDeliveries')->will($this->returnValue(array($delivery)));

        $selector = new Selector($deliveryPool, $productPool);
        $this->assertEquals(array(), $selector->getAvailableMethods($basket));
    }

    /**
     * Try to fetch a delivery based on an undefined code
     */
    public function testGetAvailableMethodsWithUndefinedCode()
    {
        $deliveryPool = $this->getMock('Sonata\Component\Delivery\Pool');
        $productPool = new ProductPool;

        $basket  = $this->getMock('Sonata\Component\Basket\Basket');
        $basketElement = $this->getMock('Sonata\Component\Basket\BasketElement');
        $product = $this->getMock('Sonata\Tests\Component\Delivery\Product');

        $delivery = $this->getMock('Sonata\Component\Product\DeliveryInterface');
        $delivery->expects($this->any())->method('getCode')->will($this->returnValue('deliveryTest'));

        $deliveryPool->expects($this->any())->method('getMethod')->will($this->returnValue(null));

        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue(array($basketElement)));
        $basketElement->expects($this->once())->method('getProduct')->will($this->returnValue($product));
        $product->expects($this->once())->method('getDeliveries')->will($this->returnValue(array($delivery)));

        $selector = new Selector($deliveryPool, $productPool);
        $this->assertEquals(array(), $selector->getAvailableMethods($basket));
    }

    /**
     * Try to fetch a delivery not having a handled country
     */
    public function testGetAvailableMethodsWithUncoveredCountry()
    {
        $deliveryPool = $this->getMock('Sonata\Component\Delivery\Pool');
        $productPool = new ProductPool;

        $basket  = $this->getMock('Sonata\Component\Basket\Basket');
        $basketElement = $this->getMock('Sonata\Component\Basket\BasketElement');
        $product = $this->getMock('Sonata\Tests\Component\Delivery\Product');

        $delivery = $this->getMock('Sonata\Component\Product\DeliveryInterface');
        $delivery->expects($this->any())->method('getCode')->will($this->returnValue('deliveryTest'));
        $delivery->expects($this->any())->method('getCountryCode')->will($this->returnValue('US'));

        $serviceDelivery = $this->getMock('Sonata\Component\Delivery\BaseServiceDelivery');
        $serviceDelivery->expects($this->any())->method('getCode')->will($this->returnValue('deliveryTest'));
        $serviceDelivery->expects($this->any())->method('getEnabled')->will($this->returnValue(true));
        $serviceDelivery->expects($this->any())->method('isAddressRequired')->will($this->returnValue(true));
        $deliveryPool->expects($this->any())->method('getMethod')->will($this->returnValue($serviceDelivery));

        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue(array($basketElement)));
        $basketElement->expects($this->once())->method('getProduct')->will($this->returnValue($product));
        $product->expects($this->once())->method('getDeliveries')->will($this->returnValue(array($delivery)));

        $address = $this->getMock('Sonata\CustomerBundle\Entity\BaseAddress');
        $address->expects($this->exactly(2))->method('getCountryCode')->will($this->returnValue('FR'));

        $selector = new Selector($deliveryPool, $productPool);
        $this->assertEquals(array(), $selector->getAvailableMethods($basket, $address));
    }

    /**
     * Provide twice the same delivery code
     */
    public function testGetAvailableMethodsWithAlreadySelectedCode()
    {
        $deliveryPool = $this->getMock('Sonata\Component\Delivery\Pool');

        $basket  = $this->getMock('Sonata\Component\Basket\Basket');
        $basketElement = $this->getMock('Sonata\Component\Basket\BasketElement');
        $product = $this->getMock('Sonata\Tests\Component\Delivery\Product');

        $delivery1 = $this->getMock('Sonata\Component\Product\DeliveryInterface');
        $delivery1->expects($this->any())->method('getCode')->will($this->returnValue('deliveryTest'));

        $delivery2 = $this->getMock('Sonata\Component\Product\DeliveryInterface');
        $delivery2->expects($this->any())->method('getCode')->will($this->returnValue('deliveryTest'));

        $serviceDelivery = $this->getMock('Sonata\Component\Delivery\BaseServiceDelivery');
        $serviceDelivery->expects($this->any())->method('getCode')->will($this->returnValue('deliveryTest'));
        $serviceDelivery->expects($this->any())->method('getEnabled')->will($this->returnValue(true));
        $serviceDelivery->expects($this->any())->method('isAddressRequired')->will($this->returnValue(false));
        $deliveryPool->expects($this->once())->method('getMethod')->will($this->returnValue($serviceDelivery));

        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue(array($basketElement)));
        $basketElement->expects($this->once())->method('getProduct')->will($this->returnValue($product));
        $product->expects($this->once())->method('getDeliveries')->will($this->returnValue(array($delivery1, $delivery2)));

        $selector = new Selector($deliveryPool, new ProductPool());

        $this->assertEquals(array($serviceDelivery), $selector->getAvailableMethods($basket));
    }

    public function testAvailableMethods()
    {
        $deliveryMethod_low = new ServiceDelivery();
        $deliveryMethod_low->setCode('ups_low');
        $deliveryMethod_low->setEnabled(true);
        $deliveryMethod_low->setPriority(1);

        $deliveryMethod_high = new ServiceDelivery();
        $deliveryMethod_high->setCode('ups_high');
        $deliveryMethod_high->setEnabled(true);
        $deliveryMethod_high->setPriority(2);

        $deliveryMethod_high_bis = new ServiceDelivery();
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



class ServiceDelivery extends \Sonata\Component\Delivery\BaseServiceDelivery
{
    public function isAddressRequired()
    {
        return false;
    }
}

class Product extends BaseProduct
{
    protected $id;

    /**
     * Get id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

}