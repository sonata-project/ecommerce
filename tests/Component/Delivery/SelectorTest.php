<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Tests\Delivery;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Delivery\Pool as DeliveryPool;
use Sonata\Component\Delivery\Selector;
use Sonata\Component\Product\Pool as ProductPool;
use Sonata\ProductBundle\Entity\BaseProduct;

class SelectorTest extends TestCase
{
    public function testEmptyBasket()
    {
        $deliveryPool = new DeliveryPool();
        $productPool = new ProductPool();

        $selector = new Selector($deliveryPool, $productPool);
        $this->assertEmpty($selector->getAvailableMethods());
    }

    public function testNonExistentProduct()
    {
        $deliveryPool = new DeliveryPool();
        $productPool = new ProductPool();

        $basketElement = $this->createMock('Sonata\Component\Basket\BasketElementInterface');

        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue([$basketElement]));

        $address = $this->createMock('Sonata\Component\Customer\AddressInterface');

        $selector = new Selector($deliveryPool, $productPool);
        $this->assertEmpty($selector->getAvailableMethods($basket, $address));
    }

    /**
     * Test the getAvailableMethods methods with no basket nor address provided.
     */
    public function testGetAvailableMethodsWithoutBasket()
    {
        $deliveryPool = new DeliveryPool();
        $productPool = new ProductPool();

        $selector = new Selector($deliveryPool, $productPool);
        $this->assertEmpty($selector->getAvailableMethods());
    }

    /**
     * Test the getAvailableMethods methods with an empty basket provided.
     */
    public function testGetAvailableMethodsWithEmptyBasket()
    {
        $deliveryPool = new DeliveryPool();
        $productPool = new ProductPool();
        $basket = $this->createMock('Sonata\Component\Basket\Basket');
        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue([]));

        $selector = new Selector($deliveryPool, $productPool);
        $this->assertEmpty($selector->getAvailableMethods($basket));
    }

    /**
     * Test the getAvailableMethods methods with a product provided but no address and no related delivery methods.
     */
    public function testGetAvailableMethodsWithFilledBasket()
    {
        $deliveryPool = new DeliveryPool();
        $productPool = new ProductPool();

        $basket = $this->createMock('Sonata\Component\Basket\Basket');
        $basketElement = $this->createMock('Sonata\Component\Basket\BasketElement');
        $product = $this->createMock('Sonata\Component\Tests\Delivery\Product');

        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue([$basketElement]));
        $basketElement->expects($this->once())->method('getProduct')->will($this->returnValue($product));
        $product->expects($this->once())->method('getDeliveries')->will($this->returnValue([]));

        $selector = new Selector($deliveryPool, $productPool);
        $this->assertEmpty($selector->getAvailableMethods($basket));
    }

    /**
     * Provide a delivery method that require an address but none is provided.
     */
    public function testGetAvailableMethodsWithRequiredAddressDelivery()
    {
        $deliveryPool = $this->createMock('Sonata\Component\Delivery\Pool');
        $productPool = new ProductPool();

        $basket = $this->createMock('Sonata\Component\Basket\Basket');
        $basketElement = $this->createMock('Sonata\Component\Basket\BasketElement');
        $product = $this->createMock('Sonata\Component\Tests\Delivery\Product');

        $delivery = $this->createMock('Sonata\Component\Product\DeliveryInterface');
        $delivery->expects($this->any())->method('getCode')->will($this->returnValue('deliveryTest'));

        $serviceDelivery = $this->createMock('Sonata\Component\Delivery\BaseServiceDelivery');
        $serviceDelivery->expects($this->any())->method('getCode')->will($this->returnValue('deliveryTest'));
        $serviceDelivery->expects($this->any())->method('getEnabled')->will($this->returnValue(true));
        $serviceDelivery->expects($this->any())->method('isAddressRequired')->will($this->returnValue(true));
        $deliveryPool->expects($this->any())->method('getMethod')->will($this->returnValue($serviceDelivery));

        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue([$basketElement]));
        $basketElement->expects($this->once())->method('getProduct')->will($this->returnValue($product));
        $product->expects($this->once())->method('getDeliveries')->will($this->returnValue([$delivery]));

        $selector = new Selector($deliveryPool, $productPool);
        $this->assertEquals([], $selector->getAvailableMethods($basket));
    }

    /**
     * Provide a delivery method that require an address but none is provided.
     */
    public function testGetAvailableMethodsWithDisabledDelivery()
    {
        $deliveryPool = $this->createMock('Sonata\Component\Delivery\Pool');
        $productPool = new ProductPool();

        $basket = $this->createMock('Sonata\Component\Basket\Basket');
        $basketElement = $this->createMock('Sonata\Component\Basket\BasketElement');
        $product = $this->createMock('Sonata\Component\Tests\Delivery\Product');

        $delivery = $this->createMock('Sonata\Component\Product\DeliveryInterface');
        $delivery->expects($this->any())->method('getCode')->will($this->returnValue('deliveryTest'));

        $serviceDelivery = $this->createMock('Sonata\Component\Delivery\BaseServiceDelivery');
        $serviceDelivery->expects($this->any())->method('getCode')->will($this->returnValue('deliveryTest'));
        $deliveryPool->expects($this->any())->method('getMethod')->will($this->returnValue($serviceDelivery));
        $serviceDelivery->expects($this->any())->method('getEnabled')->will($this->returnValue(false));

        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue([$basketElement]));
        $basketElement->expects($this->once())->method('getProduct')->will($this->returnValue($product));
        $product->expects($this->once())->method('getDeliveries')->will($this->returnValue([$delivery]));

        $selector = new Selector($deliveryPool, $productPool);
        $this->assertEquals([], $selector->getAvailableMethods($basket));
    }

    /**
     * Try to fetch a delivery based on an undefined code.
     */
    public function testGetAvailableMethodsWithUndefinedCode()
    {
        $deliveryPool = $this->createMock('Sonata\Component\Delivery\Pool');
        $productPool = new ProductPool();

        $basket = $this->createMock('Sonata\Component\Basket\Basket');
        $basketElement = $this->createMock('Sonata\Component\Basket\BasketElement');
        $product = $this->createMock('Sonata\Component\Tests\Delivery\Product');

        $delivery = $this->createMock('Sonata\Component\Product\DeliveryInterface');
        $delivery->expects($this->any())->method('getCode')->will($this->returnValue('deliveryTest'));

        $deliveryPool->expects($this->any())->method('getMethod')->will($this->returnValue(null));

        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue([$basketElement]));
        $basketElement->expects($this->once())->method('getProduct')->will($this->returnValue($product));
        $product->expects($this->once())->method('getDeliveries')->will($this->returnValue([$delivery]));

        $selector = new Selector($deliveryPool, $productPool);
        $this->assertEquals([], $selector->getAvailableMethods($basket));
    }

    /**
     * Try to fetch a delivery not having a handled country.
     */
    public function testGetAvailableMethodsWithUncoveredCountry()
    {
        $deliveryPool = $this->createMock('Sonata\Component\Delivery\Pool');
        $productPool = new ProductPool();

        $basket = $this->createMock('Sonata\Component\Basket\Basket');
        $basketElement = $this->createMock('Sonata\Component\Basket\BasketElement');
        $product = $this->createMock('Sonata\Component\Tests\Delivery\Product');

        $delivery = $this->createMock('Sonata\Component\Product\DeliveryInterface');
        $delivery->expects($this->any())->method('getCode')->will($this->returnValue('deliveryTest'));
        $delivery->expects($this->any())->method('getCountryCode')->will($this->returnValue('US'));

        $serviceDelivery = $this->createMock('Sonata\Component\Delivery\BaseServiceDelivery');
        $serviceDelivery->expects($this->any())->method('getCode')->will($this->returnValue('deliveryTest'));
        $serviceDelivery->expects($this->any())->method('getEnabled')->will($this->returnValue(true));
        $serviceDelivery->expects($this->any())->method('isAddressRequired')->will($this->returnValue(true));
        $deliveryPool->expects($this->any())->method('getMethod')->will($this->returnValue($serviceDelivery));

        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue([$basketElement]));
        $basketElement->expects($this->once())->method('getProduct')->will($this->returnValue($product));
        $product->expects($this->once())->method('getDeliveries')->will($this->returnValue([$delivery]));

        $address = $this->createMock('Sonata\CustomerBundle\Entity\BaseAddress');
        $address->expects($this->exactly(2))->method('getCountryCode')->will($this->returnValue('FR'));

        $selector = new Selector($deliveryPool, $productPool);
        $this->assertEquals([], $selector->getAvailableMethods($basket, $address));
    }

    /**
     * Provide twice the same delivery code.
     */
    public function testGetAvailableMethodsWithAlreadySelectedCode()
    {
        $deliveryPool = $this->createMock('Sonata\Component\Delivery\Pool');

        $basket = $this->createMock('Sonata\Component\Basket\Basket');
        $basketElement = $this->createMock('Sonata\Component\Basket\BasketElement');
        $product = $this->createMock('Sonata\Component\Tests\Delivery\Product');

        $delivery1 = $this->createMock('Sonata\Component\Product\DeliveryInterface');
        $delivery1->expects($this->any())->method('getCode')->will($this->returnValue('deliveryTest'));

        $delivery2 = $this->createMock('Sonata\Component\Product\DeliveryInterface');
        $delivery2->expects($this->any())->method('getCode')->will($this->returnValue('deliveryTest'));

        $serviceDelivery = $this->createMock('Sonata\Component\Delivery\BaseServiceDelivery');
        $serviceDelivery->expects($this->any())->method('getCode')->will($this->returnValue('deliveryTest'));
        $serviceDelivery->expects($this->any())->method('getEnabled')->will($this->returnValue(true));
        $serviceDelivery->expects($this->any())->method('isAddressRequired')->will($this->returnValue(false));
        $deliveryPool->expects($this->once())->method('getMethod')->will($this->returnValue($serviceDelivery));

        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue([$basketElement]));
        $basketElement->expects($this->once())->method('getProduct')->will($this->returnValue($product));
        $product->expects($this->once())->method('getDeliveries')->will($this->returnValue([$delivery1, $delivery2]));

        $selector = new Selector($deliveryPool, new ProductPool());

        $this->assertEquals([$serviceDelivery], $selector->getAvailableMethods($basket));
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

        $deliveryPool = new DeliveryPool();
        $deliveryPool->addMethod($deliveryMethod_low);
        $deliveryPool->addMethod($deliveryMethod_high);
        $deliveryPool->addMethod($deliveryMethod_high_bis);

        $productPool = new ProductPool();

        $productDelivery_low = $this->createMock('Sonata\Component\Product\DeliveryInterface');
        $productDelivery_low->expects($this->any())->method('getCode')->will($this->returnValue('ups_low'));

        $productDelivery_high = $this->createMock('Sonata\Component\Product\DeliveryInterface');
        $productDelivery_high->expects($this->any())->method('getCode')->will($this->returnValue('ups_high'));

        $productDelivery_high_bis = $this->createMock('Sonata\Component\Product\DeliveryInterface');
        $productDelivery_high_bis->expects($this->any())->method('getCode')->will($this->returnValue('ups_high_bis'));

        $product = $this->createMock('Sonata\Component\Product\ProductInterface');
        $product->expects($this->once())->method('getDeliveries')->will($this->returnValue([$productDelivery_low, $productDelivery_high, $productDelivery_high_bis]));

        $basketElement = $this->createMock('Sonata\Component\Basket\BasketElementInterface');
        $basketElement->expects($this->once())->method('getProduct')->will($this->returnValue($product));

        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue([$basketElement]));

        $address = $this->createMock('Sonata\Component\Customer\AddressInterface');

        $selector = new Selector($deliveryPool, $productPool);
        $selector->setLogger($this->createMock('Symfony\Component\HttpKernel\Log\LoggerInterface'));

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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
