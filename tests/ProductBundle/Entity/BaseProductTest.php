<?php

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
    public function testGetImageAndGetGallery()
    {
        $product = new Product();

        $this->assertNull($product->getImage());

        // Test gallery
        $gallery = $this->createMock('Sonata\MediaBundle\Model\GalleryInterface');
        $product->setGallery($gallery);

        $this->assertNull($product->getImage());
        $this->assertInstanceOf('Sonata\MediaBundle\Model\GalleryInterface', $product->getGallery());

        // Test getImage
        $image = $this->createMock('Sonata\MediaBundle\Model\MediaInterface');
        $image->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('correctMedia'));

        $product->setImage($image);

        $this->assertInstanceOf('Sonata\MediaBundle\Model\MediaInterface', $product->getImage());
        $this->assertEquals('correctMedia', $product->getImage()->getName());
    }

    public function testHasOneMainCategory()
    {
        $product = new Product();

        $pc = $this->createMock('Sonata\Component\Product\ProductCategoryInterface');
        $pc->expects($this->any())->method('getMain')->will($this->returnValue(true));

        $pc2 = $this->createMock('Sonata\Component\Product\ProductCategoryInterface');
        $pc2->expects($this->any())->method('getMain')->will($this->returnValue(true));

        $pc3 = $this->createMock('Sonata\Component\Product\ProductCategoryInterface');
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
