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
        $pool = new Pool;
        $pool->addProduct(array(
            'code'       => 'fake_product',
            'class'      => 'Sonata\\Tests\Component\\Basket\\Product',
            'repository' => new ProductRepository
        ));
        
        $basket = new Basket;
        $basket->setProductsPool($pool);

        $product = new Product;

        $this->assertFalse($basket->hasProduct($product), '::hasProduct() - The product is not present in the basket');

        $basket_element = $basket->addProduct($product);

        $this->assertInstanceOf('Sonata\\Component\\Basket\\BasketElement', $basket_element, '::addProduct() - return a BasketElement');
        $this->assertEquals(1, $basket_element->getQuantity(), '::getQuantity() - return 1');
        $this->assertEquals(15, $basket->getTotal(), '::getTotal() w/o vat return 15');
        $this->assertEquals(17.94, $basket->getTotal(true), '::getTotal() w/ vat return 17.94');

        $basket_element->setQuantity(2);

        $this->assertEquals(2, $basket_element->getQuantity(), '::getQuantity() - return 2');
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

        $this->assertTrue($basket->hasElements(), '::hasElement() return true ');
        $this->assertEquals(1, $basket->countElements(), '::countElements() return 1');
        $this->assertNotEmpty($basket->getElements(), '::getElements() is not empty');

        $this->assertInstanceOf('Sonata\\Component\\Basket\\BasketElement', $element = $basket->getElement($product), '::getElement() - return a BasketElement');

        $this->assertInstanceOf('Sonata\\Component\\Basket\\BasketElement', $basket->removeElement($element), '::removeElement() - return the removed BasketElement');

        $this->assertFalse($basket->hasElements(), '::hasElement() return false');
        $this->assertEquals(0, $basket->countElements(), '::countElements() return 0');
        $this->assertEmpty($basket->getElements(), '::getElements() is empty');

        $basket_element = $basket->addProduct($product, array('quantity' => 0));
        $this->assertEquals(0, $basket_element->getQuantity(),  '::getQuantity() return 1 after adding the product');
        $basket_element = $basket->mergeProduct($product, array('quantity' => 3));

        $this->assertInstanceOf('Sonata\\Component\\Basket\\BasketElement', $basket_element, '::mergeProduct() - return the a BasketElement');
        $this->assertEquals(3, $basket_element->getQuantity(),  '::getQuantity() return 3 after product mege');

        $this->assertEquals(165, $basket->getTotal(), '::getTotal() - return 150');        

        $basket->reset();
        $this->assertFalse($basket->isValid(), '::isValid() return false after reset');
    }
}