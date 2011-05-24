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

use Application\Sonata\PaymentBundle\Entity\Transaction;

abstract class BaseProductController extends Controller
{

    public function viewAction($product) {

        if (!is_object($product)) {
            throw new NotFoundHttpException('invalid product instance');
        }

        $form = $this->get('session')->getFlash('sonata.product.form');

        $provider = $this->get('sonata.product.pool')->getProvider($product);

        if (!$form) {
           $formBuilder = $this->get('form.factory')->createNamedBuilder('form', 'add_basket');
           $form = $provider->defineAddBasketForm($product, $formBuilder)->getForm()->createView();
        }

        return $this->render(sprintf('%s:view.html.twig', 'SonataProductBundle:Amazon' /*$provider->getBaseControllerName()*/), array(
           'product' => $product,
           'form'    => $form,
        ));
    }

    public function renderFormBasketElementAction($fieldGroup, $basketElement)
    {
        $provider = $this->get('sonata.product.pool')->getProvider($basketElement->getProduct());

        return $this->render(sprintf('%s:form_basket_element.html.twig', $provider->getBaseControllerName()), array(
           'fieldGroup'    => $fieldGroup,
           'basketElement' => $basketElement,
        ));
    }

    public function renderFinalReviewBasketElementAction($basketElement)
    {
        $provider = $this->get('sonata.product.pool')->getProvider($basketElement->getProduct());

        return $this->render(sprintf('%s:final_review_basket_element.html.twig', $provider->getBaseControllerName()), array(
           'basketElement' => $basketElement,
        ));
    }

    public function viewVariationsAction($productId, $slug) {

    }

    public function viewBasketElement($basketElement) {

    }

    public function viewBasketElementConfirmation($basketElement) {

    }

    public function viewOrderElement($orderElement) {

    }

    public function viewEditOrderElement($orderElement) {

    }

}