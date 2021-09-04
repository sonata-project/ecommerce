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

namespace Sonata\BasketBundle\Tests\Controller\Api;

use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use PHPUnit\Framework\TestCase;
use Sonata\BasketBundle\Controller\Api\BasketController;
use Sonata\Component\Basket\BasketBuilderInterface;
use Sonata\Component\Basket\BasketElementInterface;
use Sonata\Component\Basket\BasketElementManagerInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Basket\BasketManagerInterface;
use Sonata\Component\Product\Pool;
use Sonata\Component\Product\ProductDefinition;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Product\ProductManagerInterface;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class BasketControllerTest extends TestCase
{
    public function testGetBasketsAction(): void
    {
        $pager = $this->createStub(PagerInterface::class);
        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects(static::once())->method('getPager')->willReturn($pager);

        $paramFetcher = $this->createMock(ParamFetcherInterface::class);
        $paramFetcher->expects(static::exactly(3))->method('get')->willReturn(1, 10, null);
        $paramFetcher->expects(static::once())->method('all')->willReturn([]);

        static::assertSame($pager, $this->createBasketController($basketManager)->getBasketsAction($paramFetcher));
    }

    public function testGetBasketAction(): void
    {
        $basket = $this->createMock(BasketInterface::class);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects(static::once())->method('findOneBy')->willReturn($basket);

        static::assertSame($basket, $this->createBasketController($basketManager)->getBasketAction(1));
    }

    public function testGetBasketActionNotFoundException(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Basket (42) not found');

        $this->createBasketController()->getBasketAction(42);
    }

    public function testGetBasketelementsAction(): void
    {
        $elements = [1, 2];

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects(static::once())->method('getBasketElements')->willReturn($elements);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects(static::once())->method('findOneBy')->willReturn($basket);

        static::assertSame($elements, $this->createBasketController($basketManager)->getBasketBasketelementsAction(1));
    }

    public function testGetBasketelementsActionNotFoundException(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Basket (42) not found');

        $this->createBasketController()->getBasketBasketelementsAction(42);
    }

    public function testPostBasketAction(): void
    {
        $basket = $this->createMock(BasketInterface::class);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects(static::once())->method('save')->willReturn($basket);

        $form = $this->createMock(Form::class);
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isSubmitted')->willReturn(true);
        $form->expects(static::once())->method('isValid')->willReturn(true);
        $form->expects(static::once())->method('getData')->willReturn($basket);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createBasketController($basketManager, null, null, null, $formFactory)->postBasketAction(new Request());

        static::assertInstanceOf(View::class, $view);
    }

    public function testPostBasketInvalidAction(): void
    {
        $basket = $this->createMock(BasketInterface::class);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects(static::never())->method('save')->willReturn($basket);

        $form = $this->createMock(Form::class);
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isSubmitted')->willReturn(true);
        $form->expects(static::once())->method('isValid')->willReturn(false);
        $form->expects(static::never())->method('getData')->willReturn($basket);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createBasketController($basketManager, null, null, null, $formFactory)->postBasketAction(new Request());

        static::assertInstanceOf(FormInterface::class, $view);
    }

    public function testPutBasketAction(): void
    {
        $basket = $this->createMock(BasketInterface::class);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects(static::once())->method('save')->willReturn($basket);
        $basketManager->expects(static::once())->method('findOneBy')->willReturn($basket);

        $form = $this->createMock(Form::class);
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isSubmitted')->willReturn(true);
        $form->expects(static::once())->method('isValid')->willReturn(true);
        $form->expects(static::once())->method('getData')->willReturn($basket);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createBasketController($basketManager, null, null, null, $formFactory)->putBasketAction(1, new Request());

        static::assertInstanceOf(View::class, $view);
    }

    public function testPutBasketInvalidAction(): void
    {
        $basket = $this->createMock(BasketInterface::class);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects(static::never())->method('save')->willReturn($basket);
        $basketManager->expects(static::once())->method('findOneBy')->willReturn($basket);

        $form = $this->createMock(Form::class);
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isSubmitted')->willReturn(true);
        $form->expects(static::once())->method('isValid')->willReturn(false);
        $form->expects(static::never())->method('getData')->willReturn($basket);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createBasketController($basketManager, null, null, null, $formFactory)->putBasketAction(1, new Request());

        static::assertInstanceOf(FormInterface::class, $view);
    }

    public function testDeleteBasketAction(): void
    {
        $basket = $this->createMock(BasketInterface::class);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects(static::once())->method('delete');
        $basketManager->expects(static::once())->method('findOneBy')->willReturn($basket);

        $view = $this->createBasketController($basketManager)->deleteBasketAction(1);

        static::assertSame(['deleted' => true], $view);
    }

    public function testDeleteBasketInvalidAction(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects(static::once())->method('findOneBy')->willReturn(null);
        $basketManager->expects(static::never())->method('delete');

        $this->createBasketController($basketManager)->deleteBasketAction(1);
    }

    public function testPostBasketBasketelementsAction(): void
    {
        $productDefinition = $this->getMockBuilder(ProductDefinition::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productPool = $this->createMock(Pool::class);
        $productPool->expects(static::once())->method('getProduct')->willReturn($productDefinition);

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects(static::once())->method('getProductPool')->willReturn($productPool);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects(static::once())->method('save')->willReturn($basket);
        $basketManager->expects(static::once())->method('findOneBy')->willReturn($basket);

        $basketElement = $this->createMock(BasketElementInterface::class);

        $basketElementManager = $this->createMock(BasketElementManagerInterface::class);
        $basketElementManager->expects(static::once())->method('create')->willReturn($basketElement);

        $product = $this->createMock(ProductInterface::class);

        $productManager = $this->createMock(ProductManagerInterface::class);
        $productManager->expects(static::once())->method('findOneBy')->willReturn($product);

        $basketBuilder = $this->createMock(BasketBuilderInterface::class);
        $basketBuilder->expects(static::once())->method('build');

        $form = $this->createMock(Form::class);
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isSubmitted')->willReturn(true);
        $form->expects(static::once())->method('isValid')->willReturn(true);
        $form->expects(static::once())->method('getData')->willReturn($basketElement);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createBasketController($basketManager, $basketElementManager, $productManager, $basketBuilder, $formFactory)->postBasketBasketelementsAction(1, new Request());

        static::assertInstanceOf(View::class, $view);
    }

    public function testPostBasketBasketelementsInvalidAction(): void
    {
        $productDefinition = $this->getMockBuilder(ProductDefinition::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productPool = $this->createMock(Pool::class);
        $productPool->expects(static::once())->method('getProduct')->willReturn($productDefinition);

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects(static::once())->method('getProductPool')->willReturn($productPool);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects(static::never())->method('save')->willReturn($basket);
        $basketManager->expects(static::once())->method('findOneBy')->willReturn($basket);

        $basketElement = $this->createMock(BasketElementInterface::class);

        $basketElementManager = $this->createMock(BasketElementManagerInterface::class);
        $basketElementManager->expects(static::once())->method('create')->willReturn($basketElement);

        $product = $this->createMock(ProductInterface::class);

        $productManager = $this->createMock(ProductManagerInterface::class);
        $productManager->expects(static::once())->method('findOneBy')->willReturn($product);

        $basketBuilder = $this->createMock(BasketBuilderInterface::class);
        $basketBuilder->expects(static::once())->method('build');

        $form = $this->createMock(Form::class);
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isSubmitted')->willReturn(true);
        $form->expects(static::once())->method('isValid')->willReturn(false);
        $form->expects(static::never())->method('getData');

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createBasketController($basketManager, $basketElementManager, $productManager, $basketBuilder, $formFactory)->postBasketBasketelementsAction(1, new Request());

        static::assertInstanceOf(FormInterface::class, $view);
    }

    public function testPutBasketBasketelementsAction(): void
    {
        $productDefinition = $this->getMockBuilder(ProductDefinition::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productPool = $this->createMock(Pool::class);
        $productPool->expects(static::once())->method('getProduct')->willReturn($productDefinition);

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects(static::once())->method('getProductPool')->willReturn($productPool);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects(static::never())->method('save')->willReturn($basket);
        $basketManager->expects(static::once())->method('findOneBy')->willReturn($basket);

        $basketElement = $this->createMock(BasketElementInterface::class);

        $basketElementManager = $this->createMock(BasketElementManagerInterface::class);
        $basketElementManager->expects(static::once())->method('findOneBy')->willReturn($basketElement);
        $basketElementManager->expects(static::once())->method('save')->willReturn($basketElement);
        $basketElementManager->expects(static::never())->method('create')->willReturn($basketElement);

        $product = $this->createMock(ProductInterface::class);

        $productManager = $this->createMock(ProductManagerInterface::class);
        $productManager->expects(static::once())->method('findOneBy')->willReturn($product);

        $basketBuilder = $this->createMock(BasketBuilderInterface::class);
        $basketBuilder->expects(static::once())->method('build');

        $form = $this->createMock(Form::class);
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isSubmitted')->willReturn(true);
        $form->expects(static::once())->method('isValid')->willReturn(true);
        $form->expects(static::once())->method('getData')->willReturn($basketElement);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createBasketController($basketManager, $basketElementManager, $productManager, $basketBuilder, $formFactory)->putBasketBasketelementsAction(1, 1, new Request());

        static::assertInstanceOf(View::class, $view);
    }

    public function testPutBasketBasketelementsInvalidAction(): void
    {
        $productDefinition = $this->getMockBuilder(ProductDefinition::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productPool = $this->createMock(Pool::class);
        $productPool->expects(static::once())->method('getProduct')->willReturn($productDefinition);

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects(static::once())->method('getProductPool')->willReturn($productPool);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects(static::never())->method('save')->willReturn($basket);
        $basketManager->expects(static::once())->method('findOneBy')->willReturn($basket);

        $basketElement = $this->createMock(BasketElementInterface::class);

        $basketElementManager = $this->createMock(BasketElementManagerInterface::class);
        $basketElementManager->expects(static::once())->method('findOneBy')->willReturn($basketElement);
        $basketElementManager->expects(static::never())->method('create')->willReturn($basketElement);

        $product = $this->createMock(ProductInterface::class);

        $productManager = $this->createMock(ProductManagerInterface::class);
        $productManager->expects(static::once())->method('findOneBy')->willReturn($product);

        $basketBuilder = $this->createMock(BasketBuilderInterface::class);
        $basketBuilder->expects(static::once())->method('build');

        $form = $this->createMock(Form::class);
        $form->expects(static::once())->method('handleRequest');
        $form->expects(static::once())->method('isSubmitted')->willReturn(true);
        $form->expects(static::once())->method('isValid')->willReturn(false);
        $form->expects(static::never())->method('getData');

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects(static::once())->method('createNamed')->willReturn($form);

        $view = $this->createBasketController($basketManager, $basketElementManager, $productManager, $basketBuilder, $formFactory)->putBasketBasketelementsAction(1, 1, new Request());

        static::assertInstanceOf(FormInterface::class, $view);
    }

    public function testDeleteBasketBasketelementsAction(): void
    {
        $basket = $this->createMock(BasketInterface::class);
        $basket->expects(static::once())->method('getBasketElements')->willReturn([]);
        $basket->expects(static::once())->method('setBasketElements');

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects(static::once())->method('findOneBy')->willReturn($basket);
        $basketManager->expects(static::once())->method('save');

        $basketBuilder = $this->createMock(BasketBuilderInterface::class);
        $basketBuilder->expects(static::once())->method('build');

        $view = $this->createBasketController($basketManager, null, null, $basketBuilder)->deleteBasketBasketelementsAction(1, 1);

        static::assertInstanceOf(View::class, $view);
    }

    /**
     * @param $basketManager
     * @param $basketElementManager
     * @param $productManager
     * @param $basketBuilder
     * @param $formFactory
     *
     * @return BasketController
     */
    public function createBasketController($basketManager = null, $basketElementManager = null, $productManager = null, $basketBuilder = null, $formFactory = null)
    {
        if (null === $basketManager) {
            $basketManager = $this->createMock(BasketManagerInterface::class);
        }
        if (null === $basketElementManager) {
            $basketElementManager = $this->createMock(BasketElementManagerInterface::class);
        }
        if (null === $productManager) {
            $productManager = $this->createMock(ProductManagerInterface::class);
        }
        if (null === $basketBuilder) {
            $basketBuilder = $this->createMock(BasketBuilderInterface::class);
        }
        if (null === $formFactory) {
            $formFactory = $this->createMock(FormFactoryInterface::class);
        }

        return new BasketController($basketManager, $basketElementManager, $productManager, $basketBuilder, $formFactory);
    }
}
