<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\Test\BasketBundle\Controller\Api;

use Sonata\BasketBundle\Controller\Api\BasketController;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class BasketControllerTest
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class BasketControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetBasketsAction()
    {
        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');

        $basketManager = $this->getMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('findBy')->will($this->returnValue(array($basket)));

        $paramFetcher = $this->getMock('FOS\RestBundle\Request\ParamFetcherInterface');
        $paramFetcher->expects($this->exactly(3))->method('get');
        $paramFetcher->expects($this->once())->method('all')->will($this->returnValue(array()));

        $this->assertEquals(array($basket), $this->createBasketController(null, $basketManager)->getBasketsAction($paramFetcher));
    }

    public function testGetBasketAction()
    {
        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');
        $this->assertEquals($basket, $this->createBasketController($basket)->getBasketAction(1));
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
        $elements = array(1, 2);

        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');
        $basket->expects($this->once())->method('getBasketElements')->will($this->returnValue($elements));

        $this->assertEquals($elements, $this->createBasketController($basket)->getBasketBasketelementsAction(1));
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
        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');

        $basketManager = $this->getMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('save')->will($this->returnValue($basket));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('bind');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($basket));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createBasketController(null, $basketManager, $formFactory)->postBasketAction(new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPostBasketInvalidAction()
    {
        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');

        $basketManager = $this->getMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->never())->method('save')->will($this->returnValue($basket));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('bind');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->never())->method('getData')->will($this->returnValue($basket));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createBasketController(null, $basketManager, $formFactory)->postBasketAction(new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testPutBasketAction()
    {
        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');

        $basketManager = $this->getMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('save')->will($this->returnValue($basket));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('bind');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $form->expects($this->once())->method('getData')->will($this->returnValue($basket));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createBasketController($basket, $basketManager, $formFactory)->putBasketAction(1, new Request());

        $this->assertInstanceOf('FOS\RestBundle\View\View', $view);
    }

    public function testPutBasketInvalidAction()
    {
        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');

        $basketManager = $this->getMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->never())->method('save')->will($this->returnValue($basket));

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')->disableOriginalConstructor()->getMock();
        $form->expects($this->once())->method('bind');
        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $form->expects($this->never())->method('getData')->will($this->returnValue($basket));

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory->expects($this->once())->method('createNamed')->will($this->returnValue($form));

        $view = $this->createBasketController($basket, $basketManager, $formFactory)->putBasketAction(1, new Request());

        $this->assertInstanceOf('Symfony\Component\Form\FormInterface', $view);
    }

    public function testDeleteBasketAction()
    {
        $basket = $this->getMock('Sonata\Component\Basket\BasketInterface');

        $basketManager = $this->getMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('delete');

        $view = $this->createBasketController($basket, $basketManager)->deleteBasketAction(1);

        $this->assertEquals(array('deleted' => true), $view);
    }

    public function testDeleteBasketInvalidAction()
    {
        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $basketManager = $this->getMock('Sonata\Component\Basket\BasketManagerInterface');
        $basketManager->expects($this->once())->method('findOneBy')->will($this->returnValue(null));
        $basketManager->expects($this->never())->method('delete');

        $this->createBasketController(null, $basketManager)->deleteBasketAction(1);
    }

    /**
     * @param $basket
     * @param $basketManager
     * @param $formFactory
     *
     * @return BasketController
     */
    public function createBasketController($basket = null, $basketManager = null, $formFactory = null)
    {
        if (null === $basketManager) {
            $basketManager = $this->getMock('Sonata\Component\Basket\BasketManagerInterface');
        }
        if (null !== $basket) {
            $basketManager->expects($this->once())->method('findOneBy')->will($this->returnValue($basket));
        }
        if (null === $formFactory) {
            $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        }

        return new BasketController($basketManager, $formFactory);
    }
}
