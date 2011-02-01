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
use Sonata\Tests\Component\Basket\ProductRepository;
use Sonata\Tests\Component\Basket\Delivery;
use Sonata\Tests\Component\Basket\Payment;

class BasketTest extends \PHPUnit_Framework_TestCase
{

    public function testBasket()
    {
        $product = new Product;

        $basket = new Basket;
        
        $repository = $this->getMock('ProductRepository', array('getClassMetadata', 'basketMergeProduct', 'basketAddProduct', 'basketCalculatePrice', 'isAddableToBasket'));
        $repository->expects($this->any())
            ->method('basketAddProduct')
            ->will($this->returnCallback(function($bsket, $product, $params = array()) use ($basket, $repository) {

           
                $basketElement = new BasketElement;
                $basketElement->setQuantity(isset($params['quantity']) ? $params['quantity'] : 1);
                $basketElement->setProduct($product, $repository);

                $basket->addBasketElement($basketElement);

                return $basketElement;
            }));


        $repository->expects($this->any())
            ->method('basketMergeProduct')
            ->will($this->returnCallback(function($baskt, $product, $params = array()) use ($basket, $repository) {


                $basketElement = new BasketElement;
                $basketElement->setQuantity(isset($params['quantity']) ? $params['quantity'] : 1);
                $basketElement->setProduct($product, $repository);

                $basket->addBasketElement($basketElement);

                return $basketElement;
            }));
                

        $repository->expects($this->any())
            ->method('basketCalculatePrice')
            ->will($this->returnValue(15));

        $repository->expects($this->any())
            ->method('isAddableToBasket')
            ->will($this->returnValue(true));

        $entity_manager = $this->getMock('EntityManager', array('getRepository'));
        $entity_manager->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repository));


        $pool = new Pool;
        $pool->addProduct(array(
            'id'         => 'fake_product',
            'class'      => 'Sonata\\Tests\Component\\Basket\\Product',
        ));
        $pool->setEntityManager($entity_manager);

        $basket->setProductPool($pool);

        $this->assertFalse($basket->hasProduct($product), '::hasProduct() - The product is not present in the basket');

        $basketElement = $basket->addProduct($product);
        
        $this->assertInstanceOf('Sonata\\Component\\Basket\\BasketElement', $basketElement, '::addProduct() - return a BasketElement');
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

        $this->assertTrue($basket->isValid(true), '::isValid() return true for element only');
        $this->assertFalse($basket->isValid(), '::isValid() return false for the complete check');

        $payment = new Payment;
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
        $this->assertEmpty($basket->geBaskettElements(), '::getElements() is empty');

        $basketElement = $basket->addProduct($product, array('quantity' => 0));
        $this->assertEquals(0, $basketElement->getQuantity(),  '::getQuantity() return 1 after adding the product');
        $basketElement = $basket->mergeProduct($product, array('quantity' => 3));

        $this->assertInstanceOf('Sonata\\Component\\Basket\\BasketElement', $basketElement, '::mergeProduct() - return the a BasketElement');
        $this->assertEquals(3, $basketElement->getQuantity(),  '::getQuantity() return 3 after product mege');

        $this->assertEquals(165, $basket->getTotal(), '::getTotal() - return 150');        

        $basket->reset();
        $this->assertFalse($basket->isValid(), '::isValid() return false after reset');
    }

    public function testSerialize()
    {

        $product = $this->getMock('Product', array('getId'));
        $product->expects($this->exactly(1), array('getId'))
            ->method('getId')
            ->will($this->returnValue(3));

        $basketElement = $this->getMock('BasketElement', array('getProduct', 'setPos'));
        $basketElement->expects($this->exactly(2))
            ->method('getProduct')
            ->will($this->returnValue($product))
        ;

        $basketElement->expects($this->once())
            ->method('setPos')
        ;

        $product_pool = $this->getMock('ProductPool', array('getRepository'));
        $product_pool->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue(false));

        $basket = new Basket;

        $basket->setProductPool($product_pool);

        $basket->addBasketElement($basketElement);

        $data = $basket->serialize();

        $this->assertTrue(is_string($data));
        $this->assertStringStartsWith('a:7:', $data, 'the serialize array has 7 elements');

        $basket->reset();
        $this->assertTrue(count($basket->getBasketElements()) == 0, '::reset() remove all elements');
        $basket->unserialize($data);
        $this->assertTrue(count($basket->getBasketElements()) == 1, '::unserialize() restore elements');
    }
}