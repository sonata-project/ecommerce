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

use Sonata\Component\Basket\BasketElement;

use Doctrine\ORM\Mapping\ClassMetadata;

class BasketElementTest extends \PHPUnit_Framework_TestCase
{
    public function getBasketElement($product = null)
    {
        if(!$product)
        {
            $product = new Product;
        }
        
        $classmetadata = new \stdClass;
        $classmetadata->discriminatorValue = 'test';

        $repository = $this->getMock('ProductRepository', array('getClassMetadata'));
        $repository
            ->expects($this->once())
            ->method('getClassMetadata')
            ->will($this->returnValue($classmetadata));


        $basketElement = new BasketElement;
        $basketElement->setProduct($product, $repository);

        return $basketElement;
    }
    public function testPrice()
    {

        $basketElement = $this->getBasketElement();

        $this->assertEquals(19.6, $basketElement->getVat(), 'BasketElement returns the correct VAT');
        $this->assertEquals(1, $basketElement->getQuantity(), 'BasketElement returns the correct default quantity');

        $this->assertEquals(15, $basketElement->getUnitPrice(), 'BasketElement return the correct price w/o VAT');
        $this->assertEquals(17.94, $basketElement->getUnitPrice(true), 'BasketElement return the correct price w/ VAT');

        $this->assertEquals(15, $basketElement->getTotal(), 'BasketElement return the correct price w/o VAT');
        $this->assertEquals(17.94, $basketElement->getTotal(true), 'BasketElement return the correct price w VAT');

        $this->assertEquals(2.94, $basketElement->getVatAmount(), 'BasketElement returns the correct VAT amount');
    }

    public function testOptions()
    {
        $basketElement = $this->getBasketElement();

//        $this->assertEquals(true, $basketElement->hasOption('option1'), 'BasketElement has one option : option1');
//        $this->assertEquals(false, $basketElement->hasOption('fake'), 'BasketElement has not option : fake');

        $this->assertEquals('toto', $basketElement->getOption('option1'), 'option1 option = toto');
        $this->assertEquals(null, $basketElement->getOption('fake'), 'fake option = null');
    }

    public function testQuantity()
    {
        $basketElement = $this->getBasketElement();

        $basketElement->setQuantity(10);

        $this->assertEquals(19.6, $basketElement->getVat(), 'BasketElement returns the correct VAT');
        $this->assertEquals(179.4, $basketElement->getTotal(true), 'BasketElement returns the correct price w/ VAT');
    }

    public function testValidatity()
    {
        $product = new Product;
        $basketElement = $this->getBasketElement($product);
        
        $this->assertEquals(true, $basketElement->isValid(), 'BasketElement is valid');

        $product->enabled = false;
        $this->assertEquals(false, $basketElement->isValid(), 'BasketElement returns the correct default quantity');
    }

}