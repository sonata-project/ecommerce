<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\Test\ProductBundle\Controller\Api;

use Sonata\ProductBundle\Controller\Api\ProductController;


/**
 * Class ProductControllerTest
 *
 * @package Sonata\Test\ProductBundle\Controller\Api
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class ProductControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetProductsAction()
    {
        $product        = $this->getMock('Sonata\Component\Product\ProductInterface');
        $productManager = $this->getMock('Sonata\Component\Product\ProductManagerInterface');
        $productManager->expects($this->once())->method('findBy')->will($this->returnValue(array($product)));

        $paramFetcher = $this->getMock('FOS\RestBundle\Request\ParamFetcherInterface');
        $paramFetcher->expects($this->exactly(3))->method('get');
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue(array()));

        $this->assertEquals(array($product), $this->createProductController(null, $productManager)->getProductsAction($paramFetcher));
    }

    public function testGetProductAction()
    {
        $product = $this->getMock('Sonata\Component\Product\ProductInterface');
        $this->assertEquals($product, $this->createProductController($product)->getProductAction(1));
    }

    /**
     * @expectedException        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Product (42) not found
     */
    public function testGetProductActionNotFoundException()
    {
        $this->createProductController()->getProductAction(42);
    }

    public function testGetProductProductcategoriesAction()
    {
        $product         = $this->getMock('Sonata\Component\Product\ProductInterface');
        $productCategory = $this->getMock('Sonata\Component\Product\ProductCategoryInterface');
        $product->expects($this->once())->method('getProductCategories')->will($this->returnValue(array($productCategory)));

        $this->assertEquals(array($productCategory), $this->createProductController($product)->getProductProductcategoriesAction(1));
    }

    public function testGetProductCategoriesAction()
    {
        $product  = $this->getMock('Sonata\Component\Product\ProductInterface');
        $category = $this->getMock('Sonata\ClassificationBundle\Model\CategoryInterface');
        $product->expects($this->once())->method('getCategories')->will($this->returnValue(array($category)));

        $this->assertEquals(array($category), $this->createProductController($product)->getProductCategoriesAction(1));
    }

    public function testGetProductProductcollectionsAction()
    {
        $product           = $this->getMock('Sonata\Component\Product\ProductInterface');
        $productCollection = $this->getMock('Sonata\Component\Product\ProductCollectionInterface');
        $product->expects($this->once())->method('getProductCollections')->will($this->returnValue(array($productCollection)));

        $this->assertEquals(array($productCollection), $this->createProductController($product)->getProductProductcollectionsAction(1));
    }

    public function testGetProductCollectionsAction()
    {
        $product    = $this->getMock('Sonata\Component\Product\ProductInterface');
        $collection = $this->getMock('Sonata\ClassificationBundle\Model\CollectionInterface');
        $product->expects($this->once())->method('getCollections')->will($this->returnValue(array($collection)));

        $this->assertEquals(array($collection), $this->createProductController($product)->getProductCollectionsAction(1));
    }

    public function testGetProductPackagesAction()
    {
        $product = $this->getMock('Sonata\Component\Product\ProductInterface');
        $package = $this->getMock('Sonata\Component\Product\PackageInterface');
        $product->expects($this->once())->method('getPackages')->will($this->returnValue(array($package)));

        $this->assertEquals(array($package), $this->createProductController($product)->getProductPackagesAction(1));
    }

    public function testGetProductDeliveriesAction()
    {
        $product  = $this->getMock('Sonata\Component\Product\ProductInterface');
        $delivery = $this->getMock('Sonata\Component\Product\DeliveryInterface');
        $product->expects($this->once())->method('getDeliveries')->will($this->returnValue(array($delivery)));

        $this->assertEquals(array($delivery), $this->createProductController($product)->getProductDeliveriesAction(1));
    }

    public function testGetProductVariationsAction()
    {
        $product   = $this->getMock('Sonata\Component\Product\ProductInterface');
        $variation = $this->getMock('Sonata\Component\Product\ProductInterface');
        $product->expects($this->once())->method('getVariations')->will($this->returnValue(array($variation)));

        $this->assertEquals(array($variation), $this->createProductController($product)->getProductVariationsAction(1));
    }

    /**
     * @param $product
     * @param $productManager
     *
     * @return ProductController
     */
    public function createProductController($product = null, $productManager = null)
    {
        if (null === $productManager) {
            $productManager = $this->getMock('Sonata\Component\Product\ProductManagerInterface');
        }
        if (null !== $product) {
            $productManager->expects($this->once())->method('findOneBy')->will($this->returnValue($product));
        }

        return new ProductController($productManager);
    }
}
