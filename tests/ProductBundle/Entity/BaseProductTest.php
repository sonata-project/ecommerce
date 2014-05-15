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
            ->will($this->returnValue("correctMedia"));

        $product->setImage($image);

        $this->assertInstanceOf('Sonata\MediaBundle\Model\MediaInterface', $product->getImage());
        $this->assertEquals("correctMedia", $product->getImage()->getName());
    }
    
    public function testGetMainCategoryWithoutMainCategory()
    {
        $product = new Product();
                     
        $this->assertNull($product->getMainCategory());
       
       
        $category = $this->getMock('Sonata\ClassificationBundle\Model\CategoryInterface');                     
        $productCategory = $this->getMock('Sonata\Component\Product\ProductCategoryInterface');       
        $productCategory->setProduct($product);
        $productCategory->setCategory($category);
        $productCategory->expects($this->any())
            ->method('getMain')
            ->will($this->returnValue(false));
        $productCategory->expects($this->any())
            ->method('getEnabled')
            ->will($this->returnValue(true));
       
        $product->addProductCategorie($productCategory);
        $this->assertNull($product->getMainCategory());
       
    }
   
    public function testGetMainCategory()
    {
        $product = new Product();                    
       
        $category = $this->getMock('Sonata\ClassificationBundle\Model\CategoryInterface'); 
        $category->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('correctMainCategory'));
       
        $productCategory = $this->getMock('Sonata\Component\Product\ProductCategoryInterface');       
        $productCategory->setProduct($product);
        $productCategory->setCategory($category);
        $productCategory->setMain(true);

        $productCategory->expects($this->any())
            ->method('getMain')
            ->will($this->returnValue(true));
        $productCategory->setEnabled(true);

        $productCategory->expects($this->any())
            ->method('getEnabled')
            ->will($this->returnValue(true));
       
        $product->addProductCategorie($productCategory);               
       
       
        $this->assertInstanceOf('Sonata\ClassificationBundle\Model\CategoryInterface', $product->getMainCategory());

        $this->assertEquals("correctMainCategory", $product->getMainCategory()->getName());       
    }    
}
