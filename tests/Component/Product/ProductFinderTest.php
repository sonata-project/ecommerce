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
use Sonata\Component\Product\ProductFinder;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\ProductManagerInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class ProductFinderTest extends TestCase
{
    public function testGetCrossSellingSimilarProducts(): void
    {
        $productManager = $this->createMock(ProductManagerInterface::class);
        $productManager->expects($this->once())
            ->method('findInSameCollections')
            ->will($this->returnValue([]));

        $finder = new ProductFinder($productManager);

        $product = $this->createMock(ProductInterface::class);
        $this->assertSame([], $finder->getCrossSellingSimilarProducts($product));
    }
}
