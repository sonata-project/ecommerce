<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\Component\Product;

use Sonata\Component\Product\Pool;
use Sonata\Component\Product\ProductDefinition;

class ProductPoolTest extends \PHPUnit_Framework_TestCase
{
    public function testPool()
    {
        $productProvider = $this->getMock('Sonata\Component\Product\ProductProviderInterface');
        $productManager1 = $this->getMock('Sonata\Component\Product\ProductManagerInterface');
        $productManager2 = $this->getMock('Sonata\Component\Product\ProductManagerInterface');

        // we need products from different objects to test ProductPool
        $product1 = $this->getMock('Sonata\Component\Product\ProductInterface');
        $product2 = new Product();

        $productManager1->expects($this->any())
            ->method('getClass')
            ->will($this->returnValue($product1));

        $productManager2->expects($this->any())
            ->method('getClass')
            ->will($this->returnValue($product2));

        $definition1 = new ProductDefinition($productProvider, $productManager1);
        $definition2 = new ProductDefinition($productProvider, $productManager2);

        $productPool = new Pool;
        $productPool->addProduct('product1', $definition1);
        $productPool->addProduct('product2', $definition2);

        $this->assertFalse($productPool->hasProvider('grou'));
        $this->assertTrue($productPool->hasProvider('product1'));
        $this->assertTrue($productPool->hasProvider('product2'));

        $this->assertEquals($productPool->getProduct('product1'), $definition1);
        $this->assertEquals($productPool->getProduct('product2'), $definition2);

        $this->assertEquals($productPool->getProductCode($product1), 'product1');
        $this->assertEquals($productPool->getProductCode($product2), 'product2');
    }
}
