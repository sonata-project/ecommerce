<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\FormView;

use Sonata\Component\Basket\BasketElementInterface;
use Sonata\Component\Order\OrderElementInterface;
use Sonata\Component\Basket\BasketInterface;

abstract class BaseProductController extends Controller
{
    /**
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @param $product
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function viewAction($product)
    {
        if (!is_object($product)) {
            throw new NotFoundHttpException('invalid product instance');
        }

        $form     = $this->get('session')->getFlashBag()->get('sonata.product.form');
        $provider = $this->get('sonata.product.pool')->getProvider($product);

        if (!$form) {
            $formBuilder = $this->get('form.factory')->createNamedBuilder('add_basket', 'form', null, array('data_class' => $this->container->getParameter('sonata.basket.basket_element.class')));
            $provider->defineAddBasketForm($product, $formBuilder);

            $form = $formBuilder->getForm()->createView();
        }

        return $this->render(sprintf('%s:view.html.twig', $provider->getBaseControllerName()), array(
           'product' => $product,
           'form'    => $form,
        ));
    }

    /**
     * @param  \Symfony\Component\Form\FormView                    $formView
     * @param  \Sonata\Component\Basket\BasketElementInterface     $basketElement
     * @param  \Sonata\Component\Basket\BasketInterface            $basket
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function renderFormBasketElementAction(FormView $formView, BasketElementInterface $basketElement, BasketInterface $basket)
    {
        $provider = $this->get('sonata.product.pool')->getProvider($basketElement->getProduct());

        return $this->render(sprintf('%s:form_basket_element.html.twig', $provider->getBaseControllerName()), array(
            'formView'      => $formView,
            'basketElement' => $basketElement,
            'basket'        => $basket
        ));
    }

    /**
     * @param  \Sonata\Component\Basket\BasketElementInterface     $basketElement
     * @param  \Sonata\Component\Basket\BasketInterface            $basket
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function renderFinalReviewBasketElementAction(BasketElementInterface $basketElement, BasketInterface $basket)
    {
        $provider = $this->get('sonata.product.pool')->getProvider($basketElement->getProduct());

        return $this->render(sprintf('%s:final_review_basket_element.html.twig', $provider->getBaseControllerName()), array(
            'basketElement' => $basketElement,
            'basket'        => $basket
        ));
    }

    /**
     * @param string $productId
     * @param string $slug
     */
    abstract public function viewVariationsAction($productId, $slug)
    {

    }

    /**
     * @param BasketElementInterface $basketElement
     */
    abstract public function viewBasketElement(BasketElementInterface $basketElement)
    {

    }

    /**
     * @param BasketElementInterface $basketElement
     */
    abstract public function viewBasketElementConfirmation(BasketElementInterface $basketElement)
    {

    }

    /**
     * @param OrderElementInterface $orderElement
     */
    abstract public function viewOrderElement(OrderElementInterface $orderElement)
    {

    }

    /**
     * @param OrderElementInterface $orderElement
     */
    abstract public function viewEditOrderElement(OrderElementInterface $orderElement)
    {

    }
}
