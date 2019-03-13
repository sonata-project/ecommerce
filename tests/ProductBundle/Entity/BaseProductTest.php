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

        $this->assertNull($product->getImage());

        // Test gallery
        $gallery = $this->createMock(GalleryInterface::class);
        $product->setGallery($gallery);

        $this->assertNull($product->getImage());
        $this->assertInstanceOf(GalleryInterface::class, $product->getGallery());

        // Test getImage
        $image = $this->createMock(MediaInterface::class);
        $image->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('correctMedia'));

        $product->setImage($image);

        $this->assertInstanceOf(MediaInterface::class, $product->getImage());
        $this->assertSame('correctMedia', $product->getImage()->getName());
    }

    public function testHasOneMainCategory(): void
    {
        $product = new Product();

        $pc = $this->createMock(ProductCategoryInterface::class);
        $pc->expects($this->any())->method('getMain')->will($this->returnValue(true));

        $pc2 = $this->createMock(ProductCategoryInterface::class);
        $pc2->expects($this->any())->method('getMain')->will($this->returnValue(true));

        $pc3 = $this->createMock(ProductCategoryInterface::class);
        $pc3->expects($this->any())->method('getMain')->will($this->returnValue(true));

        $this->assertFalse($product->hasOneMainCategory());

        $product->addProductCategory($pc);
        $this->assertTrue($product->hasOneMainCategory());

        $product->addProductCategory($pc2);
        $this->assertFalse($product->hasOneMainCategory());

        $product->addProductCategory($pc3);
        $this->assertFalse($product->hasOneMainCategory());
    }
}
