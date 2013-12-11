<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Tests\ProductBundle\Entity;

use Sonata\ProductBundle\Entity\BaseProduct;

/**
 * Class Product
 *
 * @package Sonata\Tests\ProductBundle\Entity
 *
 * @author  Hugo Briand <briand@ekino.com>
 */
class Product extends BaseProduct
{
    /**
     * Get id.
     *
     * @return integer
     */
    public function getId()
    {
        // TODO: Implement getId() method.
    }

}

/**
 * Class BaseProductTest
 *
 * @package Sonata\Tests\ProductBundle\Entity
 *
 * @author  Hugo Briand <briand@ekino.com>
 */
class BaseProductTest extends \PHPUnit_Framework_TestCase
{
    public function testGetImage()
    {
        $product = new Product();

        $this->assertNull($product->getImage());

        $gallery = $this->getMock('Sonata\MediaBundle\Model\GalleryInterface');
        $product->setGallery($gallery);

        $this->assertNull($product->getImage());

        $media = $this->getMock('Sonata\MediaBundle\Model\MediaInterface');

        $galleryHasMedia = $this->getMock('Sonata\MediaBundle\Model\GalleryHasMediaInterface');
        $galleryHasMedia->expects($this->once())
            ->method('getMedia')
            ->will($this->returnValue($media));

        $gallery = $this->getMock('Sonata\MediaBundle\Model\GalleryInterface');
        $gallery->expects($this->once())
            ->method('getGalleryHasMedias')
            ->will($this->returnValue(array($galleryHasMedia)));

        $product->setGallery($gallery);

        $this->assertInstanceOf('Sonata\MediaBundle\Model\MediaInterface', $product->getImage());
    }
}