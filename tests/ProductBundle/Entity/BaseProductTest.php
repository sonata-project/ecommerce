<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\ProductBundle\Entity;

use Sonata\ProductBundle\Entity\BaseProduct;

/**
 * Class Product.
 *
 *
 * @author  Hugo Briand <briand@ekino.com>
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
 * Class BaseProductTest.
 *
 *
 * @author  Hugo Briand <briand@ekino.com>
 */
class BaseProductTest extends \PHPUnit_Framework_TestCase
{
    public function testGetImageAndGetGallery()
    {
        $product = new Product();

        $this->assertNull($product->getImage());

        // Test gallery
        $gallery = $this->getMock('Sonata\MediaBundle\Model\GalleryInterface');
        $product->setGallery($gallery);

        $this->assertNull($product->getImage());
        $this->assertInstanceOf('Sonata\MediaBundle\Model\GalleryInterface', $product->getGallery());

        // Test getImage
        $image = $this->getMock('Sonata\MediaBundle\Model\MediaInterface');
        $image->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('correctMedia'));

        $product->setImage($image);

        $this->assertInstanceOf('Sonata\MediaBundle\Model\MediaInterface', $product->getImage());
        $this->assertSame('correctMedia', $product->getImage()->getName());
    }
}
