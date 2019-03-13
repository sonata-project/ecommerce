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
        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->once())->method('getPager')->will($this->returnValue([]));

        $paramFetcher = $this->createMock(ParamFetcherInterface::class);
        $paramFetcher->expects($this->exactly(3))->method('get');
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue([]));

        $this->assertSame([], $this->createBasketController($basketManager)->getBasketsAction($paramFetcher));
    }

    public function testGetBasketAction(): void
    {
        $basket = $this->createMock(BasketInterface::class);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->once())->method('findOneBy')->will($this->returnValue($basket));

        $this->assertSame($basket, $this->createBasketController($basketManager)->getBasketAction(1));
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
        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue($elements));

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->once())->method('findOneBy')->will($this->returnValue($basket));

        $this->assertSame($elements, $this->createBasketController($basketManager)->getBasketBasketelementsAction(1));
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
        $basketManager->expects($this->once())->method('save')->will($this->returnValue($basket));

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isSubmitted')->will($this->returnValue(true));
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($basket));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createBasketController($basketManager, null, null, null, $formFactory)->postBasketAction(new Request());

        $this->assertInstanceOf(View::class, $view);
    }

    public function testPostBasketInvalidAction(): void
    {
        $basket = $this->createMock(BasketInterface::class);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->never())->method('save')->will($this->returnValue($basket));

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isSubmitted')->will($this->returnValue(true));
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->never())->method('getData')->will($this->returnValue($basket));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createBasketController($basketManager, null, null, null, $formFactory)->postBasketAction(new Request());

        $this->assertInstanceOf(FormInterface::class, $view);
    }

    public function testPutBasketAction(): void
    {
        $basket = $this->createMock(BasketInterface::class);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->once())->method('save')->will($this->returnValue($basket));
        $basketManager->expects($this->once())->method('findOneBy')->will($this->returnValue($basket));

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isSubmitted')->will($this->returnValue(true));
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($basket));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createBasketController($basketManager, null, null, null, $formFactory)->putBasketAction(1, new Request());

        $this->assertInstanceOf(View::class, $view);
    }

    public function testPutBasketInvalidAction(): void
    {
        $basket = $this->createMock(BasketInterface::class);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->never())->method('save')->will($this->returnValue($basket));
        $basketManager->expects($this->once())->method('findOneBy')->will($this->returnValue($basket));

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isSubmitted')->will($this->returnValue(true));
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->never())->method('getData')->will($this->returnValue($basket));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createBasketController($basketManager, null, null, null, $formFactory)->putBasketAction(1, new Request());

        $this->assertInstanceOf(FormInterface::class, $view);
    }

    public function testDeleteBasketAction(): void
    {
        $basket = $this->createMock(BasketInterface::class);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->once())->method('delete');
        $basketManager->expects($this->once())->method('findOneBy')->will($this->returnValue($basket));

        $view = $this->createBasketController($basketManager)->deleteBasketAction(1);

        $this->assertSame(['deleted' => true], $view);
    }

    public function testDeleteBasketInvalidAction(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->once())->method('findOneBy')->will($this->returnValue(null));
        $basketManager->expects($this->never())->method('delete');

        $this->createBasketController($basketManager)->deleteBasketAction(1);
    }

    public function testPostBasketBasketelementsAction(): void
    {
        $productDefinition = $this->getMockBuilder(ProductDefinition::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productPool = $this->createMock(Pool::class);
        $productPool->expects($this->once())->method('getProduct')->will($this->returnValue($productDefinition));

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects($this->once())->method('getProductPool')->will($this->returnValue($productPool));

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->once())->method('save')->will($this->returnValue($basket));
        $basketManager->expects($this->once())->method('findOneBy')->will($this->returnValue($basket));

        $basketElement = $this->createMock(BasketElementInterface::class);

        $basketElementManager = $this->createMock(BasketElementManagerInterface::class);
        $basketElementManager->expects($this->once())->method('create')->will($this->returnValue($basketElement));

        $product = $this->createMock(ProductInterface::class);

        $productManager = $this->createMock(ProductManagerInterface::class);
        $productManager->expects($this->once())->method('findOneBy')->will($this->returnValue($product));

        $basketBuilder = $this->createMock(BasketBuilderInterface::class);
        $basketBuilder->expects($this->once())->method('build');

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isSubmitted')->will($this->returnValue(true));
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($basketElement));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createBasketController($basketManager, $basketElementManager, $productManager, $basketBuilder, $formFactory)->postBasketBasketelementsAction(1, new Request());

        $this->assertInstanceOf(View::class, $view);
    }

    public function testPostBasketBasketelementsInvalidAction(): void
    {
        $productDefinition = $this->getMockBuilder(ProductDefinition::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productPool = $this->createMock(Pool::class);
        $productPool->expects($this->once())->method('getProduct')->will($this->returnValue($productDefinition));

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects($this->once())->method('getProductPool')->will($this->returnValue($productPool));

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->never())->method('save')->will($this->returnValue($basket));
        $basketManager->expects($this->once())->method('findOneBy')->will($this->returnValue($basket));

        $basketElement = $this->createMock(BasketElementInterface::class);

        $basketElementManager = $this->createMock(BasketElementManagerInterface::class);
        $basketElementManager->expects($this->once())->method('create')->will($this->returnValue($basketElement));

        $product = $this->createMock(ProductInterface::class);

        $productManager = $this->createMock(ProductManagerInterface::class);
        $productManager->expects($this->once())->method('findOneBy')->will($this->returnValue($product));

        $basketBuilder = $this->createMock(BasketBuilderInterface::class);
        $basketBuilder->expects($this->once())->method('build');

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isSubmitted')->will($this->returnValue(true));
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->never())->method('getData');

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createBasketController($basketManager, $basketElementManager, $productManager, $basketBuilder, $formFactory)->postBasketBasketelementsAction(1, new Request());

        $this->assertInstanceOf(FormInterface::class, $view);
    }

    public function testPutBasketBasketelementsAction(): void
    {
        $productDefinition = $this->getMockBuilder(ProductDefinition::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productPool = $this->createMock(Pool::class);
        $productPool->expects($this->once())->method('getProduct')->will($this->returnValue($productDefinition));

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects($this->once())->method('getProductPool')->will($this->returnValue($productPool));

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->never())->method('save')->will($this->returnValue($basket));
        $basketManager->expects($this->once())->method('findOneBy')->will($this->returnValue($basket));

        $basketElement = $this->createMock(BasketElementInterface::class);

        $basketElementManager = $this->createMock(BasketElementManagerInterface::class);
        $basketElementManager->expects($this->once())->method('findOneBy')->will($this->returnValue($basketElement));
        $basketElementManager->expects($this->once())->method('save')->will($this->returnValue($basketElement));
        $basketElementManager->expects($this->never())->method('create')->will($this->returnValue($basketElement));

        $product = $this->createMock(ProductInterface::class);

        $productManager = $this->createMock(ProductManagerInterface::class);
        $productManager->expects($this->once())->method('findOneBy')->will($this->returnValue($product));

        $basketBuilder = $this->createMock(BasketBuilderInterface::class);
        $basketBuilder->expects($this->once())->method('build');

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isSubmitted')->will($this->returnValue(true));
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($basketElement));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createBasketController($basketManager, $basketElementManager, $productManager, $basketBuilder, $formFactory)->putBasketBasketelementsAction(1, 1, new Request());

        $this->assertInstanceOf(View::class, $view);
    }

    public function testPutBasketBasketelementsInvalidAction(): void
    {
        $productDefinition = $this->getMockBuilder(ProductDefinition::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productPool = $this->createMock(Pool::class);
        $productPool->expects($this->once())->method('getProduct')->will($this->returnValue($productDefinition));

        $basket = $this->createMock(BasketInterface::class);
        $basket->expects($this->once())->method('getProductPool')->will($this->returnValue($productPool));

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->never())->method('save')->will($this->returnValue($basket));
        $basketManager->expects($this->once())->method('findOneBy')->will($this->returnValue($basket));

        $basketElement = $this->createMock(BasketElementInterface::class);

        $basketElementManager = $this->createMock(BasketElementManagerInterface::class);
        $basketElementManager->expects($this->once())->method('findOneBy')->will($this->returnValue($basketElement));
        $basketElementManager->expects($this->never())->method('create')->will($this->returnValue($basketElement));

        $product = $this->createMock(ProductInterface::class);

        $productManager = $this->createMock(ProductManagerInterface::class);
        $productManager->expects($this->once())->method('findOneBy')->will($this->returnValue($product));

        $basketBuilder = $this->createMock(BasketBuilderInterface::class);
        $basketBuilder->expects($this->once())->method('build');

        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isSubmitted')->will($this->returnValue(true));
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->never())->method('getData');

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createBasketController($basketManager, $basketElementManager, $productManager, $basketBuilder, $formFactory)->putBasketBasketelementsAction(1, 1, new Request());

        $this->assertInstanceOf(FormInterface::class, $view);
    }

    public function testDeleteBasketBasketelementsAction(): void
    {
        $basket = $this->createMock(BasketInterface::class);
        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue([]));
        $basket->expects($this->once())->method('setBasketElements');

        $basketManager = $this->createMock(BasketManagerInterface::class);
        $basketManager->expects($this->once())->method('findOneBy')->will($this->returnValue($basket));
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
