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

namespace Sonata\ProductBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Sonata\Component\Product\ProductCategoryInterface;
use Sonata\MediaBundle\Model\GalleryInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\ProductBundle\Entity\BaseProduct;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class Product extends BaseProduct
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        // TODO: Implement getId() method.
    }
}

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class BaseProductTest extends TestCase
{
    public function testGetImageAndGetGallery(): void
    {
        $product = new Product();

        static::assertNull($product->getImage());

        // Test gallery
        $gallery = $this->createMock(GalleryInterface::class);
        $product->setGallery($gallery);

        static::assertNull($product->getImage());
        static::assertInstanceOf(GalleryInterface::class, $product->getGallery());

        // Test getImage
        $image = $this->createMock(MediaInterface::class);
        $image->expects(static::any())
            ->method('getName')
            ->willReturn('correctMedia');

        $product->setImage($image);

        static::assertInstanceOf(MediaInterface::class, $product->getImage());
        static::assertSame('correctMedia', $product->getImage()->getName());
    }

    public function testHasOneMainCategory(): void
    {
        $product = new Product();

        $pc = $this->createMock(ProductCategoryInterface::class);
        $pc->expects(static::any())->method('getMain')->willReturn(true);

        $pc2 = $this->createMock(ProductCategoryInterface::class);
        $pc2->expects(static::any())->method('getMain')->willReturn(true);

        $pc3 = $this->createMock(ProductCategoryInterface::class);
        $pc3->expects(static::any())->method('getMain')->willReturn(true);

        static::assertFalse($product->hasOneMainCategory());

        $product->addProductCategory($pc);
        static::assertTrue($product->hasOneMainCategory());

        $product->addProductCategory($pc2);
        static::assertFalse($product->hasOneMainCategory());

        $product->addProductCategory($pc3);
        static::assertFalse($product->hasOneMainCategory());
    }
}
