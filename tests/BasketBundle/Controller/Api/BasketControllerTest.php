<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\BasketBundle\Tests\Controller\Api;

use Sonata\BasketBundle\Controller\Api\BasketController;
use Sonata\Tests\Helpers\PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class BasketControllerTest extends PHPUnit_Framework_TestCase
{
    public function testGetBasketsAction()
    {
        $basketManager = $this->createMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('getPager')->will($this->returnValue([]));

        $paramFetcher = $this->createMock('FOS\RestBundle\Request\ParamFetcherInterface');
        $paramFetcher->expects($this->exactly(3))->method('get');
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue([]));

        $this->assertEquals([], $this->createBasketController($basketManager)->getBasketsAction($paramFetcher));
    }

    public function testGetBasketAction()
    {
        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');

        $basketManager = $this->createMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('findOneBy')->will($this->returnValue($basket));

        $this->assertEquals($basket, $this->createBasketController($basketManager)->getBasketAction(1));
    }

    /**
     * @expectedException        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Basket (42) not found
     */
    public function testGetBasketActionNotFoundException()
    {
        $this->createBasketController()->getBasketAction(42);
    }

    public function testGetBasketelementsAction()
    {
        $elements = [1, 2];

        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue($elements));

        $basketManager = $this->createMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('findOneBy')->will($this->returnValue($basket));

        $this->assertEquals($elements, $this->createBasketController($basketManager)->getBasketBasketelementsAction(1));
    }

    /**
     * @expectedException        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Basket (42) not found
     */
    public function testGetBasketelementsActionNotFoundException()
    {
        $this->createBasketController()->getBasketBasketelementsAction(42);
    }

    public function testPostBasketAction()
    {
        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');

        $basketManager = $this->createMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('save')->will($this->returnValue($basket));

        $form = $this->createMock('Symfony\Component\Form\Form');
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($basket));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createBasketController($basketManager, null, null, null, $formFactory)->postBasketAction(new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPostBasketInvalidAction()
    {
        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');

        $basketManager = $this->createMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->never())->method('save')->will($this->returnValue($basket));

        $form = $this->createMock('Symfony\Component\Form\Form');
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->never())->method('getData')->will($this->returnValue($basket));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createBasketController($basketManager, null, null, null, $formFactory)->postBasketAction(new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testPutBasketAction()
    {
        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');

        $basketManager = $this->createMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('save')->will($this->returnValue($basket));
        $basketManager->expects($this->once())->method('findOneBy')->will($this->returnValue($basket));

        $form = $this->createMock('Symfony\Component\Form\Form');
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($basket));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createBasketController($basketManager, null, null, null, $formFactory)->putBasketAction(1, new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPutBasketInvalidAction()
    {
        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');

        $basketManager = $this->createMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->never())->method('save')->will($this->returnValue($basket));
        $basketManager->expects($this->once())->method('findOneBy')->will($this->returnValue($basket));

        $form = $this->createMock('Symfony\Component\Form\Form');
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->never())->method('getData')->will($this->returnValue($basket));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createBasketController($basketManager, null, null, null, $formFactory)->putBasketAction(1, new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testDeleteBasketAction()
    {
        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');

        $basketManager = $this->createMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('delete');
        $basketManager->expects($this->once())->method('findOneBy')->will($this->returnValue($basket));

        $view = $this->createBasketController($basketManager)->deleteBasketAction(1);

        $this->assertEquals(['deleted' => true], $view);
    }

    public function testDeleteBasketInvalidAction()
    {
        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $basketManager = $this->createMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('findOneBy')->will($this->returnValue(null));
        $basketManager->expects($this->never())->method('delete');

        $this->createBasketController($basketManager)->deleteBasketAction(1);
    }

    public function testPostBasketBasketelementsAction()
    {
        $productDefinition = $this->getMockBuilder('Sonata\Component\Product\ProductDefinition')
            ->disableOriginalConstructor()
            ->getMock();

        $productPool = $this->createMock('Sonata\Component\Product\Pool');
        $productPool->expects($this->once())->method('getProduct')->will($this->returnValue($productDefinition));

        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getProductPool')->will($this->returnValue($productPool));

        $basketManager = $this->createMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('save')->will($this->returnValue($basket));
        $basketManager->expects($this->once())->method('findOneBy')->will($this->returnValue($basket));

        $basketElement = $this->createMock('Sonata\Component\Basket\BasketElementInterface');

        $basketElementManager = $this->createMock('Sonata\Component\Basket\BasketElementManagerInterface');
        $basketElementManager->expects($this->once())->method('create')->will($this->returnValue($basketElement));

        $product = $this->createMock('Sonata\Component\Product\ProductInterface');

        $productManager = $this->createMock('Sonata\Component\Product\ProductManagerInterface');
        $productManager->expects($this->once())->method('findOneBy')->will($this->returnValue($product));

        $basketBuilder = $this->createMock('Sonata\Component\Basket\BasketBuilderInterface');
        $basketBuilder->expects($this->once())->method('build');

        $form = $this->createMock('Symfony\Component\Form\Form');
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($basketElement));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createBasketController($basketManager, $basketElementManager, $productManager, $basketBuilder, $formFactory)->postBasketBasketelementsAction(1, new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPostBasketBasketelementsInvalidAction()
    {
        $productDefinition = $this->getMockBuilder('Sonata\Component\Product\ProductDefinition')
            ->disableOriginalConstructor()
            ->getMock();

        $productPool = $this->createMock('Sonata\Component\Product\Pool');
        $productPool->expects($this->once())->method('getProduct')->will($this->returnValue($productDefinition));

        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getProductPool')->will($this->returnValue($productPool));

        $basketManager = $this->createMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->never())->method('save')->will($this->returnValue($basket));
        $basketManager->expects($this->once())->method('findOneBy')->will($this->returnValue($basket));

        $basketElement = $this->createMock('Sonata\Component\Basket\BasketElementInterface');

        $basketElementManager = $this->createMock('Sonata\Component\Basket\BasketElementManagerInterface');
        $basketElementManager->expects($this->once())->method('create')->will($this->returnValue($basketElement));

        $product = $this->createMock('Sonata\Component\Product\ProductInterface');

        $productManager = $this->createMock('Sonata\Component\Product\ProductManagerInterface');
        $productManager->expects($this->once())->method('findOneBy')->will($this->returnValue($product));

        $basketBuilder = $this->createMock('Sonata\Component\Basket\BasketBuilderInterface');
        $basketBuilder->expects($this->once())->method('build');

        $form = $this->createMock('Symfony\Component\Form\Form');
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->never())->method('getData');

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createBasketController($basketManager, $basketElementManager, $productManager, $basketBuilder, $formFactory)->postBasketBasketelementsAction(1, new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testPutBasketBasketelementsAction()
    {
        $productDefinition = $this->getMockBuilder('Sonata\Component\Product\ProductDefinition')
            ->disableOriginalConstructor()
            ->getMock();

        $productPool = $this->createMock('Sonata\Component\Product\Pool');
        $productPool->expects($this->once())->method('getProduct')->will($this->returnValue($productDefinition));

        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getProductPool')->will($this->returnValue($productPool));

        $basketManager = $this->createMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->never())->method('save')->will($this->returnValue($basket));
        $basketManager->expects($this->once())->method('findOneBy')->will($this->returnValue($basket));

        $basketElement = $this->createMock('Sonata\Component\Basket\BasketElementInterface');

        $basketElementManager = $this->createMock('Sonata\Component\Basket\BasketElementManagerInterface');
        $basketElementManager->expects($this->once())->method('findOneBy')->will($this->returnValue($basketElement));
        $basketElementManager->expects($this->once())->method('save')->will($this->returnValue($basketElement));
        $basketElementManager->expects($this->never())->method('create')->will($this->returnValue($basketElement));

        $product = $this->createMock('Sonata\Component\Product\ProductInterface');

        $productManager = $this->createMock('Sonata\Component\Product\ProductManagerInterface');
        $productManager->expects($this->once())->method('findOneBy')->will($this->returnValue($product));

        $basketBuilder = $this->createMock('Sonata\Component\Basket\BasketBuilderInterface');
        $basketBuilder->expects($this->once())->method('build');

        $form = $this->createMock('Symfony\Component\Form\Form');
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($basketElement));

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createBasketController($basketManager, $basketElementManager, $productManager, $basketBuilder, $formFactory)->putBasketBasketelementsAction(1, 1, new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPutBasketBasketelementsInvalidAction()
    {
        $productDefinition = $this->getMockBuilder('Sonata\Component\Product\ProductDefinition')
            ->disableOriginalConstructor()
            ->getMock();

        $productPool = $this->createMock('Sonata\Component\Product\Pool');
        $productPool->expects($this->once())->method('getProduct')->will($this->returnValue($productDefinition));

        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getProductPool')->will($this->returnValue($productPool));

        $basketManager = $this->createMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->never())->method('save')->will($this->returnValue($basket));
        $basketManager->expects($this->once())->method('findOneBy')->will($this->returnValue($basket));

        $basketElement = $this->createMock('Sonata\Component\Basket\BasketElementInterface');

        $basketElementManager = $this->createMock('Sonata\Component\Basket\BasketElementManagerInterface');
        $basketElementManager->expects($this->once())->method('findOneBy')->will($this->returnValue($basketElement));
        $basketElementManager->expects($this->never())->method('create')->will($this->returnValue($basketElement));

        $product = $this->createMock('Sonata\Component\Product\ProductInterface');

        $productManager = $this->createMock('Sonata\Component\Product\ProductManagerInterface');
        $productManager->expects($this->once())->method('findOneBy')->will($this->returnValue($product));

        $basketBuilder = $this->createMock('Sonata\Component\Basket\BasketBuilderInterface');
        $basketBuilder->expects($this->once())->method('build');

        $form = $this->createMock('Symfony\Component\Form\Form');
        $form->expects($this->once())->method('handleRequest');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->never())->method('getData');

        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createBasketController($basketManager, $basketElementManager, $productManager, $basketBuilder, $formFactory)->putBasketBasketelementsAction(1, 1, new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testDeleteBasketBasketelementsAction()
    {
        $basket = $this->createMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue([]));
        $basket->expects($this->once())->method('setBasketElements');

        $basketManager = $this->createMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('findOneBy')->will($this->returnValue($basket));
        $basketManager->expects($this->once())->method('save');

        $basketBuilder = $this->createMock('Sonata\Component\Basket\BasketBuilderInterface');
        $basketBuilder->expects($this->once())->method('build');

        $view = $this->createBasketController($basketManager, null, null, $basketBuilder)->deleteBasketBasketelementsAction(1, 1);

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
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
            $basketManager = $this->createMock('Sonata\Component\Basket\BasketManagerInterface');
        }
        if (null === $basketElementManager) {
            $basketElementManager = $this->createMock('Sonata\Component\Basket\BasketElementManagerInterface');
        }
        if (null === $productManager) {
            $productManager = $this->createMock('Sonata\Component\Product\ProductManagerInterface');
        }
        if (null === $basketBuilder) {
            $basketBuilder = $this->createMock('Sonata\Component\Basket\BasketBuilderInterface');
        }
        if (null === $formFactory) {
            $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');
        }

        return new BasketController($basketManager, $basketElementManager, $productManager, $basketBuilder, $formFactory);
    }
}
