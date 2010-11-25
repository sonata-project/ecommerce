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


class BasketElementTest extends \PHPUnit_Framework_TestCase
{
    public function testPrice()
    {
        $basket_element = new BasketElement;
        $basket_element->setProduct(new Product);

        $this->assertEquals(19.6, $basket_element->getVat(), 'BasketElement returns the correct VAT');
        $this->assertEquals(1, $basket_element->getQuantity(), 'BasketElement returns the correct default quantity');

        $this->assertEquals(15, $basket_element->getUnitPrice(), 'BasketElement return the correct price w/o VAT');
        $this->assertEquals(17.94, $basket_element->getUnitPrice(true), 'BasketElement return the correct price w/ VAT');

        $this->assertEquals(15, $basket_element->getTotal(), 'BasketElement return the correct price w/o VAT');
        $this->assertEquals(17.94, $basket_element->getTotal(true), 'BasketElement return the correct price w VAT');

        $this->assertEquals(2.94, $basket_element->getVatAmount(), 'BasketElement returns the correct VAT amount');
    }

    public function testOptions()
    {
        $basket_element = new BasketElement;
        $basket_element->setProduct(new Product);

//        $this->assertEquals(true, $basket_element->hasOption('option1'), 'BasketElement has one option : option1');
//        $this->assertEquals(false, $basket_element->hasOption('fake'), 'BasketElement has not option : fake');

        $this->assertEquals('toto', $basket_element->getOption('option1'), 'option1 option = toto');
        $this->assertEquals(null, $basket_element->getOption('fake'), 'fake option = null');
    }

    public function testQuantity()
    {
        $basket_element = new BasketElement;
        $basket_element->setProduct(new Product);
        $basket_element->setQuantity(10);

        $this->assertEquals(19.6, $basket_element->getVat(), 'BasketElement returns the correct VAT');
        $this->assertEquals(179.4, $basket_element->getTotal(true), 'BasketElement returns the correct price w/ VAT');
    }

    public function testValidatity()
    {
        $product = new Product;
        $basket_element = new BasketElement;
        $basket_element->setProduct($product);
        
        $this->assertEquals(true, $basket_element->isValid(), 'BasketElement is valid');

        $product->enabled = false;
        $this->assertEquals(false, $basket_element->isValid(), 'BasketElement returns the correct default quantity');
    }

}