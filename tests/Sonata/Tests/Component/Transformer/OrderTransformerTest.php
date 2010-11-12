<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\Component\Transformer;

use Sonata\Component\Transformer\Pool;
use Sonata\Component\Transformer\BasketTransformer;
use Sonata\Component\Transformer\OrderTransformer;


class OrderTransformerTest extends \PHPUnit_Framework_TestCase
{

    public function testBasket()
    {

        $products = array();
        
        $products[] = new \Sonata\Tests\Component\Basket\Product;
        $products[] = new \Sonata\Tests\Component\Basket\Product;

        $basket_elements = array();

        $basket_element = $this->getMock('Sonata\\Component\\Basket\\BasketElement');
        $basket_element->expects($this->any())
            ->method('getProduct')
            ->will($this->returnValue($products[0]));


        $basket_elements[] = $basket_element;

        $basket_element = $this->getMock('Sonata\\Component\\Basket\\BasketElement');
        $basket_element->expects($this->any())
            ->method('getProduct')
            ->will($this->returnValue($products[0]));

        $basket_elements[] = $basket_element;
        
        // Mock the product repository
        $repository = $this->getMock('ProductRepository', array('find', 'basketAddProduct', 'basketCalculatePrice'));

        $repository->expects($this->exactly(2))
            ->method('find')
            ->will($this->onConsecutiveCalls($products[0], $products[1]));

        $repository->expects($this->exactly(2))
            ->method('basketAddProduct')
            ->will($this->onConsecutiveCalls($basket_elements[0], $basket_elements[1]));

        $repository->expects($this->exactly(5))
            ->method('basketCalculatePrice')
            ->will($this->onConsecutiveCalls(14, 23));

        
        $product_pool = new  \Sonata\Component\Product\Pool;
        $product_pool->addProduct(array(
            'id'            => 'test',
            'class'         => 'Sonata\\Tests\\Component\\Basket\\Product',
            'repository'    => $repository
        ));

        // Mock the order elements
        $elements = array();

        $element = $this->getMock('OrderElement', array('getQuantity', 'getProductId', 'getProductType'));
        $element->expects($this->exactly(1))
            ->method('getQuantity')
            ->will($this->returnValue(5));
        
        $element->expects($this->exactly(1))
            ->method('getProductId')
            ->will($this->returnValue(2));

        $element->expects($this->exactly(1))
            ->method('getProductType')
            ->will($this->returnValue('test'));

        $elements[] = $element;
        
        $element = $this->getMock('OrderElement', array('getQuantity', 'getProductId', 'getProductType'));
        $element->expects($this->once())
            ->method('getQuantity')
            ->will($this->returnValue(2));

        $element->expects($this->once())
            ->method('getProductId')
            ->will($this->returnValue(1));

        $element->expects($this->once())
            ->method('getProductType')
            ->will($this->returnValue('test'));

        $elements[] = $element;

        // Mock the order
        $order = $this->getMock('Order', array('getOrderElements'));

        $order->expects($this->once())
            ->method('getOrderElements')
            ->will($this->returnValue($elements));

        // Mock the user
        $user = $this->getMock('User', array('getId'));


        $basket = new \Sonata\Component\Basket\Basket;
        $basket->setProductsPool($product_pool);

        // Finally, transform the order into a basket
        $transformer = new OrderTransformer;
        $transformer->setProductsPool($product_pool);

        $basket = $transformer->transformIntoBasket($user, $order, $basket);

        $this->assertEquals(2, count($basket->getElements()), '::getElements() return 2 elements');
    }
}