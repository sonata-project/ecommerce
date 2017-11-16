<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Tests\Controller\Api;

use PHPUnit\Framework\TestCase;
use Sonata\ProductBundle\Controller\Api\ProductController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class ProductControllerTest extends TestCase
{
    public function testGetProductsAction()
    {
        $productManager = $this->createMock('Sonata\Component\Product\ProductManagerInterface');
        $productManager->expects($this->once())->method('getPager')->will($this->returnValue([]));

        $paramFetcher = $this->createMock('FOS\RestBundle\Request\ParamFetcherInterface');
        $paramFetcher->expects($this->exactly(3))->method('get');
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue([]));

        $this->assertEquals([], $this->createProductController(null, $productManager)->getProductsAction($paramFetcher));
    }

    public function testGetProductAction()
    {
        $product = $this->createMock('Sonata\Component\Product\ProductInterface');
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
        $product = $this->createMock('Sonata\Component\Product\ProductInterface');
        $productCategory = $this->createMock('Sonata\Component\Product\ProductCategoryInterface');
        $product->expects($this->once())->method('getProductCategories')->will($this->returnValue([$productCategory]));

        $this->assertEquals(
            [$productCategory],
            $this->createProductController($product)->getProductProductcategoriesAction(1)
        );
    }

    public function testGetProductCategoriesAction()
    {
        $product = $this->createMock('Sonata\Component\Product\ProductInterface');
        $category = $this->createMock('Sonata\ClassificationBundle\Model\CategoryInterface');
        $product->expects($this->once())->method('getCategories')->will($this->returnValue([$category]));

        $this->assertEquals([$category], $this->createProductController($product)->getProductCategoriesAction(1));
    }

    public function testGetProductProductcollectionsAction()
    {
        $product = $this->createMock('Sonata\Component\Product\ProductInterface');
        $productCollection = $this->createMock('Sonata\Component\Product\ProductCollectionInterface');
        $product->expects($this->once())->method('getProductCollections')->will($this->returnValue([$productCollection]));

        $this->assertEquals(
            [$productCollection],
            $this->createProductController($product)->getProductProductcollectionsAction(1)
        );
    }

    public function testGetProductCollectionsAction()
    {
        $product = $this->createMock('Sonata\Component\Product\ProductInterface');
        $collection = $this->createMock('Sonata\ClassificationBundle\Model\CollectionInterface');
        $product->expects($this->once())->method('getCollections')->will($this->returnValue([$collection]));

        $this->assertEquals([$collection], $this->createProductController($product)->getProductCollectionsAction(1));
    }

    public function testGetProductPackagesAction()
    {
        $product = $this->createMock('Sonata\Component\Product\ProductInterface');
        $package = $this->createMock('Sonata\Component\Product\PackageInterface');
        $product->expects($this->once())->method('getPackages')->will($this->returnValue([$package]));

        $this->assertEquals([$package], $this->createProductController($product)->getProductPackagesAction(1));
    }

    public function testGetProductDeliveriesAction()
    {
        $product = $this->createMock('Sonata\Component\Product\ProductInterface');
        $delivery = $this->createMock('Sonata\Component\Product\DeliveryInterface');
        $product->expects($this->once())->method('getDeliveries')->will($this->returnValue([$delivery]));

        $this->assertEquals([$delivery], $this->createProductController($product)->getProductDeliveriesAction(1));
    }

    public function testGetProductVariationsAction()
    {
        $product = $this->createMock('Sonata\Component\Product\ProductInterface');
        $variation = $this->createMock('Sonata\Component\Product\ProductInterface');
        $product->expects($this->once())->method('getVariations')->will($this->returnValue([$variation]));

        $this->assertEquals([$variation], $this->createProductController($product)->getProductVariationsAction(1));
    }

    public function testPostProductAction()
    {
        $product = $this->createMock('Sonata\Component\Product\ProductInterface');

        $productManager = $this->createMock('Sonata\Component\Product\ProductManagerInterface');
        $productManager->expects($this->once())->method('save')->will($this->returnValue($product));

        $productPool = $this->createMock('Sonata\Component\Product\Pool');
        $productPool->expects($this->once())->method('getManager')->will($this->returnValue($productManager));

        $formatterPool = $this->createMock('Sonata\FormatterBundle\Formatter\Pool');
        $formatterPool->expects($this->exactly(2))->method('transform');

        $form = $this->createMock('Symfony\Component\Form\Form');
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($product));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createProductController(null, $productManager, $productPool, $formFactory, $formatterPool)
            ->postProductAction('my.test.provider', new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPostProductInvalidAction()
    {
        $product = $this->createMock('Sonata\Component\Product\ProductInterface');

        $productManager = $this->createMock('Sonata\Component\Product\ProductManagerInterface');
        $productManager->expects($this->never())->method('save');

        $productPool = $this->createMock('Sonata\Component\Product\Pool');
        $productPool->expects($this->once())->method('getManager')->will($this->returnValue($productManager));

        $formatterPool = $this->createMock('Sonata\FormatterBundle\Formatter\Pool');
        $formatterPool->expects($this->never())->method('transform');

        $form = $this->createMock('Symfony\Component\Form\Form');
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createProductController(null, $productManager, $productPool, $formFactory, $formatterPool)
            ->postProductAction('my.test.provider', new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testPutProductAction()
    {
        $product = $this->createMock('Sonata\Component\Product\ProductInterface');

        $productManager = $this->createMock('Sonata\Component\Product\ProductManagerInterface');
        $productManager->expects($this->once())->method('findOneBy')->will($this->returnValue($product));
        $productManager->expects($this->once())->method('save')->will($this->returnValue($product));

        $productPool = $this->createMock('Sonata\Component\Product\Pool');
        $productPool->expects($this->once())->method('getManager')->will($this->returnValue($productManager));

        $formatterPool = $this->createMock('Sonata\FormatterBundle\Formatter\Pool');
        $formatterPool->expects($this->exactly(2))->method('transform');

        $form = $this->createMock('Symfony\Component\Form\Form');
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($product));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createProductController($product, $productManager, $productPool, $formFactory, $formatterPool)
            ->putProductAction(1, 'my.test.provider', new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPutProductInvalidAction()
    {
        $product = $this->createMock('Sonata\Component\Product\ProductInterface');

        $productManager = $this->createMock('Sonata\Component\Product\ProductManagerInterface');
        $productManager->expects($this->once())->method('findOneBy')->will($this->returnValue($product));
        $productManager->expects($this->never())->method('save');

        $productPool = $this->createMock('Sonata\Component\Product\Pool');
        $productPool->expects($this->once())->method('getManager')->will($this->returnValue($productManager));

        $formatterPool = $this->createMock('Sonata\FormatterBundle\Formatter\Pool');
        $formatterPool->expects($this->never())->method('transform');

        $form = $this->createMock('Symfony\Component\Form\Form');
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createProductController($product, $productManager, $productPool, $formFactory, $formatterPool)
            ->putProductAction(1, 'my.test.provider', new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testDeleteProductAction()
    {
        $product = $this->createMock('Sonata\Component\Product\ProductInterface');

        $productManager = $this->createMock('Sonata\Component\Product\ProductManagerInterface');
        $productManager->expects($this->once())->method('delete');

        $productPool = $this->createMock('Sonata\Component\Product\Pool');
        $productPool->expects($this->once())->method('getManager')->will($this->returnValue($productManager));

        $view = $this->createProductController($product, $productManager, $productPool)->deleteProductAction(1);

        $this->assertEquals(['deleted' => true], $view);
    }

    public function testDeleteProductInvalidAction()
    {
        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $product = $this->createMock('Sonata\Component\Product\ProductInterface');

        $productManager = $this->createMock('Sonata\Component\Product\ProductManagerInterface');
        $productManager->expects($this->once())->method('findOneBy')->will($this->returnValue(null));
        $productManager->expects($this->never())->method('delete');

        $productPool = $this->createMock('Sonata\Component\Product\Pool');
        $productPool->expects($this->never())->method('getManager')->will($this->returnValue($productManager));

        $view = $this->createProductController($product, $productManager, $productPool)->deleteProductAction(1);

        $this->assertEquals(['deleted' => true], $view);
    }

    /**
     * @param $product
     * @param $productManager
     * @param $productPool
     * @param $formFactory
     * @param null $formatterPool
     *
     * @return ProductController
     */
    public function createProductController($product = null, $productManager = null, $productPool = null, $formFactory = null, $formatterPool = null)
    {
        if (null === $productManager) {
            $productManager = $this->createMock('Sonata\Component\Product\ProductManagerInterface');
        }
        if (null !== $product) {
            $productManager->expects($this->once())->method('findOneBy')->will($this->returnValue($product));
        }
        if (null === $productPool) {
            $productPool = $this->createMock('Sonata\Component\Product\Pool');
        }
        if (null === $formFactory) {
            $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        }
        if (null === $formatterPool) {
            $formatterPool = $this->createMock('Sonata\FormatterBundle\Formatter\Pool');
        }

        return new ProductController($productManager, $productPool, $formFactory, $formatterPool);
    }
}
