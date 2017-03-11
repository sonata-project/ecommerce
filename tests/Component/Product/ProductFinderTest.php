<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Tests\Product;

use Sonata\Component\Product\ProductFinder;
use Sonata\Tests\Helpers\PHPUnit_Framework_TestCase;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class ProductFinderTest extends PHPUnit_Framework_TestCase
{
    public function testGetCrossSellingSimilarProducts()
    {
        $productManager = $this->createMock('Sonata\Component\Product\ProductManagerInterface');
        $productManager->expects($this->once())
            ->method('findInSameCollections')
            ->will($this->returnValue(array()));

        $finder = new ProductFinder($productManager);

        $product = $this->createMock('Sonata\Component\Product\ProductInterface');
        $this->assertEquals(array(), $finder->getCrossSellingSimilarProducts($product));
    }
}
