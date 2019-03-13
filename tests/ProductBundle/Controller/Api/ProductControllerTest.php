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

namespace Sonata\ProductBundle\Tests\Controller\Api;

use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use PHPUnit\Framework\TestCase;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\Component\Product\DeliveryInterface;
use Sonata\Component\Product\PackageInterface;
use Sonata\Component\Product\Pool as ProductPool;
use Sonata\Component\Product\ProductCategoryInterface;
use Sonata\Component\Product\ProductCollectionInterface;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\ProductManagerInterface;
use Sonata\FormatterBundle\Formatter\Pool as FormatterPool;
use Sonata\ProductBundle\Controller\Api\ProductController;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class ProductControllerTest extends TestCase
{
    public function testGetProductsAction(): void
    {
        $productManager = $this->createMock(ProductManagerInterface::class);
        $productManager->expects($this->once())->method('getPager')->will($this->returnValue([]));

        $paramFetcher = $this->createMock(ParamFetcherInterface::class);
        $paramFetcher->expects($this->exactly(3))->method('get');
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue([]));

        $this->assertSame([], $this->createProductController(null, $productManager)->getProductsAction($paramFetcher));
    }

    public function testGetProductAction(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $this->assertSame($product, $this->createProductController($product)->getProductAction(1));
    }

    public function testGetProductActionNotFoundException(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Product (42) not found');

        $this->createProductController()->getProductAction(42);
    }

    public function testGetProductProductcategoriesAction(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $productCategory = $this->createMock(ProductCategoryInterface::class);
        $product->expects($this->once())->method('getProductCategories')->will($this->returnValue([$productCategory]));

        $this->assertSame(
            [$productCategory],
            $this->createProductController($product)->getProductProductcategoriesAction(1)
        );
    }

    public function testGetProductCategoriesAction(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $category = $this->createMock(CategoryInterface::class);
        $product->expects($this->once())->method('getCategories')->will($this->returnValue([$category]));

        $this->assertSame([$category], $this->createProductController($product)->getProductCategoriesAction(1));
    }

    public function testGetProductProductcollectionsAction(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $productCollection = $this->createMock(ProductCollectionInterface::class);
        $product->expects($this->once())->method('getProductCollections')->will($this->returnValue([$productCollection]));

        $this->assertSame(
            [$productCollection],
            $this->createProductController($product)->getProductProductcollectionsAction(1)
        );
    }

    public function testGetProductCollectionsAction(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $collection = $this->createMock(CollectionInterface::class);
        $product->expects($this->once())->method('getCollections')->will($this->returnValue([$collection]));

        $this->assertSame([$collection], $this->createProductController($product)->getProductCollectionsAction(1));
    }

    public function testGetProductPackagesAction(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $package = $this->createMock(PackageInterface::class);
        $product->expects($this->once())->method('getPackages')->will($this->returnValue([$package]));

        $this->assertSame([$package], $this->createProductController($product)->getProductPackagesAction(1));
    }

    public function testGetProductDeliveriesAction(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $delivery = $this->createMock(DeliveryInterface::class);
        $product->expects($this->once())->method('getDeliveries')->will($this->returnValue([$delivery]));

        $this->assertSame([$delivery], $this->createProductController($product)->getProductDeliveriesAction(1));
    }

    public function testGetProductVariationsAction(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $variation = $this->createMock(ProductInterface::class);
        $product->expects($this->once())->method('getVariations')->will($this->returnValue([$variation]));

        $this->assertSame([$variation], $this->createProductController($product)->getProductVariationsAction(1));
    }

    public function testPostProductAction(): void
    {
        $product = $this->createMock(ProductInterface::class);

        $productManager = $this->createMock(ProductManagerInterface::class);
        $productManager->expects($this->once())->method('save')->will($this->returnValue($product));

        $productPool = $this->createMock(ProductPool::class);
        $productPool->expects($this->once())->method('getManager')->will($this->returnValue($productManager));

        $formatterPool = $this->createMock(FormatterPool::class);
        $formatterPool->expects($this->exactly(2))->method('transform');

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isSubmitted')->will($this->returnValue(true));
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($product));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createProductController(null, $productManager, $productPool, $formFactory, $formatterPool)
            ->postProductAction('my.test.provider', new Request());

        $this->assertInstanceOf(View::class, $view);
    }

    public function testPostProductInvalidAction(): void
    {
        $product = $this->createMock(ProductInterface::class);

        $productManager = $this->createMock(ProductManagerInterface::class);
        $productManager->expects($this->never())->method('save');

        $productPool = $this->createMock(ProductPool::class);
        $productPool->expects($this->once())->method('getManager')->will($this->returnValue($productManager));

        $formatterPool = $this->createMock(FormatterPool::class);
        $formatterPool->expects($this->never())->method('transform');

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isSubmitted')->will($this->returnValue(true));
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createProductController(null, $productManager, $productPool, $formFactory, $formatterPool)
            ->postProductAction('my.test.provider', new Request());

        $this->assertInstanceOf(FormInterface::class, $view);
    }

    public function testPutProductAction(): void
    {
        $product = $this->createMock(ProductInterface::class);

        $productManager = $this->createMock(ProductManagerInterface::class);
        $productManager->expects($this->once())->method('findOneBy')->will($this->returnValue($product));
        $productManager->expects($this->once())->method('save')->will($this->returnValue($product));

        $productPool = $this->createMock(ProductPool::class);
        $productPool->expects($this->once())->method('getManager')->will($this->returnValue($productManager));

        $formatterPool = $this->createMock(FormatterPool::class);
        $formatterPool->expects($this->exactly(2))->method('transform');

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isSubmitted')->will($this->returnValue(true));
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($product));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createProductController($product, $productManager, $productPool, $formFactory, $formatterPool)
            ->putProductAction(1, 'my.test.provider', new Request());

        $this->assertInstanceOf(View::class, $view);
    }

    public function testPutProductInvalidAction(): void
    {
        $product = $this->createMock(ProductInterface::class);

        $productManager = $this->createMock(ProductManagerInterface::class);
        $productManager->expects($this->once())->method('findOneBy')->will($this->returnValue($product));
        $productManager->expects($this->never())->method('save');

        $productPool = $this->createMock(ProductPool::class);
        $productPool->expects($this->once())->method('getManager')->will($this->returnValue($productManager));

        $formatterPool = $this->createMock(FormatterPool::class);
        $formatterPool->expects($this->never())->method('transform');

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isSubmitted')->will($this->returnValue(true));
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createProductController($product, $productManager, $productPool, $formFactory, $formatterPool)
            ->putProductAction(1, 'my.test.provider', new Request());

        $this->assertInstanceOf(FormInterface::class, $view);
    }

    public function testDeleteProductAction(): void
    {
        $product = $this->createMock(ProductInterface::class);

        $productManager = $this->createMock(ProductManagerInterface::class);
        $productManager->expects($this->once())->method('delete');

        $productPool = $this->createMock(ProductPool::class);
        $productPool->expects($this->once())->method('getManager')->will($this->returnValue($productManager));

        $view = $this->createProductController($product, $productManager, $productPool)->deleteProductAction(1);

        $this->assertSame(['deleted' => true], $view);
    }

    public function testDeleteProductInvalidAction(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $product = $this->createMock(ProductInterface::class);

        $productManager = $this->createMock(ProductManagerInterface::class);
        $productManager->expects($this->once())->method('findOneBy')->will($this->returnValue(null));
        $productManager->expects($this->never())->method('delete');

        $productPool = $this->createMock(ProductPool::class);
        $productPool->expects($this->never())->method('getManager')->will($this->returnValue($productManager));

        $view = $this->createProductController($product, $productManager, $productPool)->deleteProductAction(1);

        $this->assertSame(['deleted' => true], $view);
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
            $productManager = $this->createMock(ProductManagerInterface::class);
        }
        if (null !== $product) {
            $productManager->expects($this->once())->method('findOneBy')->will($this->returnValue($product));
        }
        if (null === $productPool) {
            $productPool = $this->createMock(ProductPool::class);
        }
        if (null === $formFactory) {
            $formFactory = $this->createMock(FormFactoryInterface::class);
        }
        if (null === $formatterPool) {
            $formatterPool = $this->createMock(FormatterPool::class);
        }

        return new ProductController($productManager, $productPool, $formFactory, $formatterPool);
    }
}
