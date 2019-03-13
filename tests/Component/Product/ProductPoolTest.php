<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Tests\Product;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Product\Pool;
use Sonata\Component\Product\ProductDefinition;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\ProductManagerInterface;
use Sonata\Component\Product\ProductProviderInterface;

class ProductPoolTest extends TestCase
{
    public function testPool(): void
    {
        $productProvider = $this->createMock(ProductProviderInterface::class);
        $productManager1 = $this->createMock(ProductManagerInterface::class);
        $productManager2 = $this->createMock(ProductManagerInterface::class);

        // we need products from different objects to test ProductPool
        $product1 = $this->createMock(ProductInterface::class);
        $product2 = new Product();

        $productManager1->expects($this->any())
            ->method('getClass')
            ->will($this->returnValue($product1));

        $productManager2->expects($this->any())
            ->method('getClass')
            ->will($this->returnValue($product2));

        $definition1 = new ProductDefinition($productProvider, $productManager1);
        $definition2 = new ProductDefinition($productProvider, $productManager2);

        $productPool = new Pool();
        $productPool->addProduct('product1', $definition1);
        $productPool->addProduct('product2', $definition2);

        $this->assertFalse($productPool->hasProvider('grou'));
        $this->assertTrue($productPool->hasProvider('product1'));
        $this->assertTrue($productPool->hasProvider('product2'));

        $this->assertSame($productPool->getProduct('product1'), $definition1);
        $this->assertSame($productPool->getProduct('product2'), $definition2);

        $this->assertSame($productPool->getProductCode($product1), 'product1');
        $this->assertSame($productPool->getProductCode($product2), 'product2');
    }
}
