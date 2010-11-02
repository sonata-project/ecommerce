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

use Sonata\Tests\Component\Basket\ProductRepository;
use Sonata\Component\Product\Pool;

class ProductRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testVariation()
    {
        $repository = new ProductRepository();
        $repository->setVariationFields(array('name', 'price'));

        $this->assertTrue($repository->hasVariationFields(), '::hasVariationFields() return true' );
        $this->assertTrue($repository->isVariateBy('name'), '::isVariateBy() return true for existing field');
        $this->assertFalse($repository->isVariateBy('fake'), '::isVariateBy() return false for non existing field');
    }

    public function testDuplicate()
    {
        $repository = new ProductRepository();
        $repository->setVariationFields(array('Name', 'Price'));

        $product = new \Sonata\Tests\Component\Basket\Product;
        $product->setId(2);

        $variation = $repository->createVariation($product);
        $variation->setPrice(11);
        $product->addVariation($variation);

        $this->assertNull($variation->getId());
        $this->assertEquals('fake name (duplicated)', $variation->getName(), '::getName() return the duplicated name');

        $variation = $repository->createVariation($product);
        $variation->setPrice(12);
        $product->addVariation($variation);

        $variation = $repository->createVariation($product);
        $variation->setPrice(13);
        $product->addVariation($variation);

        $this->assertEquals(count($product->getVariations()), 3,  '::getVariations() returns 3 elements');

        $product->setName('test');
        $product->setVat(5.5);
        $product->setPrice(4);

        // copy the information into the variation
        $repository->copyVariation($product, 'all');

        $this->assertEquals(4, $product->getPrice(), '::getPrice() return 4');

        // price should be unchanged
        $variations = $product->getVariations();
        
        $this->assertEquals(11, $variations[0]->getPrice(), '::getPrice() return 11');
        $this->assertEquals(12, $variations[1]->getPrice(), '::getPrice() return 12');
        $this->assertEquals(13, $variations[2]->getPrice(), '::getPrice() return 13');

        // vat should be updated
        $this->assertEquals(5.5, $variations[0]->getVat(), '::getVat() return 5.5');
        $this->assertEquals(5.5, $variations[1]->getVat(), '::getVat() return 5.5');
        $this->assertEquals(5.5, $variations[2]->getVat(), '::getVat() return 5.5');

    }
}