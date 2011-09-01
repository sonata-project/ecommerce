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
use Sonata\Component\Product\ProductDefinition;
use Sonata\Tests\Component\Basket\Delivery;
use Sonata\Tests\Component\Basket\Payment;
use Sonata\Component\Product\ProductManagerInterface;

class BasketTest extends \PHPUnit_Framework_TestCase
{
    public function getMockProduct()
    {
        $product = $this->getMock('Sonata\Component\Product\ProductInterface', array(), array(), 'BasketTest_Product');
        $product->expects($this->any())->method('getId')->will($this->returnValue(42));
        $product->expects($this->any())->method('getName')->will($this->returnValue('Product name'));
        $product->expects($this->any())->method('getPrice')->will($this->returnValue(15));
        $product->expects($this->any())->method('getVat')->will($this->returnValue(19.6));
        $product->expects($this->any())->method('getOptions')->will($this->returnValue(array('foo' => 'bar')));
        $product->expects($this->any())->method('getDescription')->will($this->returnValue('product description'));
        $product->expects($this->any())->method('getEnabled')->will($this->returnValue(true));

        return $product;
    }

    public function testTotal()
    {
        $basket = new Basket;

        $provider = $this->getMock('Sonata\Component\Product\ProductProviderInterface');

        $provider->expects($this->any())
            ->method('basketCalculatePrice')
            ->will($this->returnValue(15));

        $manager = $this->getMock('Sonata\Component\Product\ProductManagerInterface');
        $manager->expects($this->any())->method('getClass')->will($this->returnValue('BasketTest_Product'));

        $definition = new ProductDefinition($provider, $manager);

        $product = $this->getMockProduct();

        $pool = new Pool;
        $pool->addProduct('product_code', $definition);

        $basket->setProductPool($pool);

        $this->assertFalse($basket->hasProduct($product), '::hasProduct() - The product is not present in the basket');

        $basketElement = new BasketElement();
        $basketElement->setProduct('product_code', $product);

        $basket->addBasketElement($basketElement);

        $this->assertTrue($basket->hasProduct($product), '::hasProduct() - The product is present in the basket');

        $this->assertEquals(1, $basketElement->getQuantity(), '::getQuantity() - return 1');
        $this->assertEquals(15, $basket->getTotal(), '::getTotal() w/o vat return 15');
        $this->assertEquals(17.94, $basket->getTotal(true), '::getTotal() w/ vat return 17.94');

        $basketElement->setQuantity(2);

        $this->assertEquals(2, $basketElement->getQuantity(), '::getQuantity() - return 2');
        $this->assertEquals(30, $basket->getTotal(), '::getTotal() w/o vat return 30');
        $this->assertEquals(35.88, $basket->getTotal(true), '::getTotal() w/ vat return true');

        $delivery = new Delivery;
        $basket->setDeliveryMethod($delivery);

        $this->assertEquals(150, $basket->getTotal(), '::getTotal() - return 150');
        $this->assertEquals(179.40, $basket->getTotal(true), '::getTotal() w/o vat return 179.40');
        $this->assertEquals(29.4, $basket->getVatAmount(),  '::getVatAmount() w/o vat return 29.4');
    }

    public function testBasket()
    {
        $basket = new Basket;

        // create the provider mock
        $provider = $this->getMock('Sonata\Component\Product\ProductProviderInterface');

        $provider->expects($this->any())
            ->method('basketCalculatePrice')
            ->will($this->returnValue(15));

        $provider->expects($this->any())
            ->method('isAddableToBasket')
            ->will($this->returnValue(true));

        // create the product manager mock
        $manager = $this->getMock('Sonata\Component\Product\ProductManagerInterface');
        $manager->expects($this->any())->method('getClass')->will($this->returnValue('BasketTest_Product'));

        $definition = new ProductDefinition($provider, $manager);


        // retrieve the product mock
        $product = $this->getMockProduct();

        $pool = new Pool;
        $pool->addProduct('product_code', $definition);

        $basket->setProductPool($pool);

        // check if the product is part of the basket
        $this->assertFalse($basket->hasProduct($product), '::hasProduct() - The product is not present in the basket');

        $basketElement = new BasketElement();
        $basketElement->setProduct('product_code', $product);

        $basket->addBasketElement($basketElement);

        $this->assertTrue($basket->hasProduct($product), '::hasProduct() - The product is not present in the basket');

        $delivery = new Delivery;
        $basket->setDeliveryMethod($delivery);


        $this->assertTrue($basket->isValid(true), '::isValid() return true for element only');
        $this->assertFalse($basket->isValid(), '::isValid() return false for the complete check');

        $payment = $this->getMock('Sonata\Component\Payment\PaymentInterface');
        $payment->expects($this->any())->method('getVat')->will($this->returnValue(19.6));
        $payment->expects($this->any())->method('getPrice')->will($this->returnValue(120));

        $basket->setPaymentMethod($payment);

        $this->assertTrue($basket->isValid(true), '::isValid() return true for element only');
        $this->assertFalse($basket->isValid(), '::isValid() return false for the complete check');

        $address = new Address;
        $basket->setPaymentAddress($address);
        $basket->setDeliveryAddress($address);

        $this->assertTrue($basket->isValid(true), '::isValid() return true for element only');
        $this->assertTrue($basket->isValid(), '::isValid() return true for the complete check');

        $this->assertTrue($basket->isAddable($product), '::isAddable() return true');
        $this->assertFalse($basket->hasRecurrentPayment(), '::hasRecurrentPayment() return false');

        $this->assertTrue($basket->hasProduct($product), '::hasProduct() return true');

        $this->assertTrue($basket->hasBasketElements(), '::hasElement() return true ');
        $this->assertEquals(1, $basket->countBasketElements(), '::countElements() return 1');
        $this->assertNotEmpty($basket->getBasketElements(), '::getElements() is not empty');

        $this->assertInstanceOf('Sonata\\Component\\Basket\\BasketElement', $element = $basket->getElement($product), '::getElement() - return a BasketElement');

        $this->assertInstanceOf('Sonata\\Component\\Basket\\BasketElement', $basket->removeElement($element), '::removeElement() - return the removed BasketElement');

        $this->assertFalse($basket->hasBasketElements(), '::hasElement() return false');
        $this->assertEquals(0, $basket->countBasketElements(), '::countElements() return 0');
        $this->assertEmpty($basket->getBasketElements(), '::getElements() is empty');

        $basket->reset();
        $this->assertFalse($basket->isValid(), '::isValid() return false after reset');
    }

    public function testSerialize()
    {
        $product = $this->getMockProduct();

        $basketElement = new BasketElement();
        $basketElement->setProduct('product_code', $product);

        $provider = $this->getMock('Sonata\Component\Product\ProductProviderInterface');
        $manager = $this->getMock('Sonata\Component\Product\ProductManagerInterface');
        $manager->expects($this->any())->method('getClass')->will($this->returnValue('BasketTest_Product'));

        $definition = new ProductDefinition($provider, $manager);

        $pool = new Pool;
        $pool->addProduct('product_code', $definition);

        $basket = new Basket;

        $basket->setProductPool($pool);

        $basket->addBasketElement($basketElement);

        $data = $basket->serialize();

        $this->assertTrue(is_string($data));
        $this->assertStringStartsWith('a:10:', $data, 'the serialize array has 7 elements');

        $basket->reset();
        $this->assertTrue(count($basket->getBasketElements()) == 0, '::reset() remove all elements');
        $basket->unserialize($data);
        $this->assertTrue(count($basket->getBasketElements()) == 1, '::unserialize() restore elements');
    }
}