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

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
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
    public function testGetBasketsAction()
    {
        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->once())->method('getPager')->willReturn([]);

        $paramFetcher = $this->createMock(ParamFetcher::class);
        $paramFetcher->expects($this->exactly(3))->method('get');
        $paramFetcher->expects($this->once())->method('all')->willReturn([]);
        $paramFetcher->expects($this->once())->method('addParam')->with($this->callback(static function ($param) {
            return $param instanceof QueryParam && $param->name = 'orderBy';
        }));

        $this->assertSame([], $this->createBasketController($basketManager)->getBasketsAction($paramFetcher));
    }

    public function testGetBasketAction()
    {
        $basket = $this->createMock(BasketInterface::class);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->once())->method('findOneBy')->willReturn($basket);

        $this->assertSame($basket, $this->createBasketController($basketManager)->getBasketAction(1));
    }

    public function testGetBasketActionNotFoundException()
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Basket (42) not found');

        $this->createBasketController()->getBasketAction(42);
    }

    public function testGetBasketelementsAction()
    {
        $elements = [1, 2];

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects($this->once())->method('getBasketElements')->willReturn($elements);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->once())->method('findOneBy')->willReturn($basket);

        $this->assertSame($elements, $this->createBasketController($basketManager)->getBasketBasketelementsAction(1));
    }

    public function testGetBasketelementsActionNotFoundException()
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Basket (42) not found');

        $this->createBasketController()->getBasketBasketelementsAction(42);
    }

    public function testPostBasketAction()
    {
        $basket = $this->createMock(BasketInterface::class);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->once())->method('save')->willReturn($basket);

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->willReturn(true);
        $form->expects($this->once())->method('getData')->willReturn($basket);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->willReturn($form);

        $view = $this->createBasketController($basketManager, null, null, null, $formFactory)->postBasketAction(new Request());

        $this->assertInstanceOf(View::class, $view);
    }

    public function testPostBasketInvalidAction()
    {
        $basket = $this->createMock(BasketInterface::class);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->never())->method('save')->willReturn($basket);

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->willReturn(false);
        $form->expects($this->never())->method('getData')->willReturn($basket);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->willReturn($form);

        $view = $this->createBasketController($basketManager, null, null, null, $formFactory)->postBasketAction(new Request());

        $this->assertInstanceOf(FormInterface::class, $view);
    }

    public function testPutBasketAction()
    {
        $basket = $this->createMock(BasketInterface::class);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->once())->method('save')->willReturn($basket);
        $basketManager->expects($this->once())->method('findOneBy')->willReturn($basket);

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->willReturn(true);
        $form->expects($this->once())->method('getData')->willReturn($basket);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->willReturn($form);

        $view = $this->createBasketController($basketManager, null, null, null, $formFactory)->putBasketAction(1, new Request());

        $this->assertInstanceOf(View::class, $view);
    }

    public function testPutBasketInvalidAction()
    {
        $basket = $this->createMock(BasketInterface::class);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->never())->method('save')->willReturn($basket);
        $basketManager->expects($this->once())->method('findOneBy')->willReturn($basket);

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->willReturn(false);
        $form->expects($this->never())->method('getData')->willReturn($basket);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->willReturn($form);

        $view = $this->createBasketController($basketManager, null, null, null, $formFactory)->putBasketAction(1, new Request());

        $this->assertInstanceOf(FormInterface::class, $view);
    }

    public function testDeleteBasketAction()
    {
        $basket = $this->createMock(BasketInterface::class);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->once())->method('delete');
        $basketManager->expects($this->once())->method('findOneBy')->willReturn($basket);

        $view = $this->createBasketController($basketManager)->deleteBasketAction(1);

        $this->assertSame(['deleted' => true], $view);
    }

    public function testDeleteBasketInvalidAction()
    {
        $this->expectException(NotFoundHttpException::class);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->once())->method('findOneBy')->willReturn(null);
        $basketManager->expects($this->never())->method('delete');

        $this->createBasketController($basketManager)->deleteBasketAction(1);
    }

    public function testPostBasketBasketelementsAction()
    {
        $productDefinition = $this->getMockBuilder(ProductDefinition::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productPool = $this->createMock(Pool::class);
        $productPool->expects($this->once())->method('getProduct')->willReturn($productDefinition);

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects($this->once())->method('getProductPool')->willReturn($productPool);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->once())->method('save')->willReturn($basket);
        $basketManager->expects($this->once())->method('findOneBy')->willReturn($basket);

        $basketElement = $this->createMock(BasketElementInterface::class);

        $basketElementManager = $this->createMock(BasketElementManagerInterface::class);
        $basketElementManager->expects($this->once())->method('create')->willReturn($basketElement);

        $product = $this->createMock(ProductInterface::class);

        $productManager = $this->createMock(ProductManagerInterface::class);
        $productManager->expects($this->once())->method('findOneBy')->willReturn($product);

        $basketBuilder = $this->createMock(BasketBuilderInterface::class);
        $basketBuilder->expects($this->once())->method('build');

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->willReturn(true);
        $form->expects($this->once())->method('getData')->willReturn($basketElement);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->willReturn($form);

        $view = $this->createBasketController($basketManager, $basketElementManager, $productManager, $basketBuilder, $formFactory)->postBasketBasketelementsAction(1, new Request());

        $this->assertInstanceOf(View::class, $view);
    }

    public function testPostBasketBasketelementsInvalidAction()
    {
        $productDefinition = $this->getMockBuilder(ProductDefinition::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productPool = $this->createMock(Pool::class);
        $productPool->expects($this->once())->method('getProduct')->willReturn($productDefinition);

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects($this->once())->method('getProductPool')->willReturn($productPool);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->never())->method('save')->willReturn($basket);
        $basketManager->expects($this->once())->method('findOneBy')->willReturn($basket);

        $basketElement = $this->createMock(BasketElementInterface::class);

        $basketElementManager = $this->createMock(BasketElementManagerInterface::class);
        $basketElementManager->expects($this->once())->method('create')->willReturn($basketElement);

        $product = $this->createMock(ProductInterface::class);

        $productManager = $this->createMock(ProductManagerInterface::class);
        $productManager->expects($this->once())->method('findOneBy')->willReturn($product);

        $basketBuilder = $this->createMock(BasketBuilderInterface::class);
        $basketBuilder->expects($this->once())->method('build');

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->willReturn(false);
        $form->expects($this->never())->method('getData');

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->willReturn($form);

        $view = $this->createBasketController($basketManager, $basketElementManager, $productManager, $basketBuilder, $formFactory)->postBasketBasketelementsAction(1, new Request());

        $this->assertInstanceOf(FormInterface::class, $view);
    }

    public function testPutBasketBasketelementsAction()
    {
        $productDefinition = $this->getMockBuilder(ProductDefinition::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productPool = $this->createMock(Pool::class);
        $productPool->expects($this->once())->method('getProduct')->willReturn($productDefinition);

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects($this->once())->method('getProductPool')->willReturn($productPool);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->never())->method('save')->willReturn($basket);
        $basketManager->expects($this->once())->method('findOneBy')->willReturn($basket);

        $basketElement = $this->createMock(BasketElementInterface::class);

        $basketElementManager = $this->createMock(BasketElementManagerInterface::class);
        $basketElementManager->expects($this->once())->method('findOneBy')->willReturn($basketElement);
        $basketElementManager->expects($this->once())->method('save')->willReturn($basketElement);
        $basketElementManager->expects($this->never())->method('create')->willReturn($basketElement);

        $product = $this->createMock(ProductInterface::class);

        $productManager = $this->createMock(ProductManagerInterface::class);
        $productManager->expects($this->once())->method('findOneBy')->willReturn($product);

        $basketBuilder = $this->createMock(BasketBuilderInterface::class);
        $basketBuilder->expects($this->once())->method('build');

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->willReturn(true);
        $form->expects($this->once())->method('getData')->willReturn($basketElement);

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->willReturn($form);

        $view = $this->createBasketController($basketManager, $basketElementManager, $productManager, $basketBuilder, $formFactory)->putBasketBasketelementsAction(1, 1, new Request());

        $this->assertInstanceOf(View::class, $view);
    }

    public function testPutBasketBasketelementsInvalidAction()
    {
        $productDefinition = $this->getMockBuilder(ProductDefinition::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productPool = $this->createMock(Pool::class);
        $productPool->expects($this->once())->method('getProduct')->willReturn($productDefinition);

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects($this->once())->method('getProductPool')->willReturn($productPool);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->never())->method('save')->willReturn($basket);
        $basketManager->expects($this->once())->method('findOneBy')->willReturn($basket);

        $basketElement = $this->createMock(BasketElementInterface::class);

        $basketElementManager = $this->createMock(BasketElementManagerInterface::class);
        $basketElementManager->expects($this->once())->method('findOneBy')->willReturn($basketElement);
        $basketElementManager->expects($this->never())->method('create')->willReturn($basketElement);

        $product = $this->createMock(ProductInterface::class);

        $productManager = $this->createMock(ProductManagerInterface::class);
        $productManager->expects($this->once())->method('findOneBy')->willReturn($product);

        $basketBuilder = $this->createMock(BasketBuilderInterface::class);
        $basketBuilder->expects($this->once())->method('build');

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->willReturn(false);
        $form->expects($this->never())->method('getData');

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->willReturn($form);

        $view = $this->createBasketController($basketManager, $basketElementManager, $productManager, $basketBuilder, $formFactory)->putBasketBasketelementsAction(1, 1, new Request());

        $this->assertInstanceOf(FormInterface::class, $view);
    }

    public function testDeleteBasketBasketelementsAction()
    {
        $basket = $this->createMock(BasketInterface::class);
        $basket->expects($this->once())->method('getBasketElements')->willReturn([]);
        $basket->expects($this->once())->method('setBasketElements');

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->once())->method('findOneBy')->willReturn($basket);
        $basketManager->expects($this->once())->method('save');

        $basketBuilder = $this->createMock(BasketBuilderInterface::class);
        $basketBuilder->expects($this->once())->method('build');

        $view = $this->createBasketController($basketManager, null, null, $basketBuilder)->deleteBasketBasketelementsAction(1, 1);

        $this->assertInstanceOf(View::class, $view);
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
