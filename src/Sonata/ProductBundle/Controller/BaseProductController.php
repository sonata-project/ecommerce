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

        if (!$form) {
            $repository = $this->get('sonata.product.pool')->getRepository($product);

            $form = $repository->getAddBasketForm($product, $this->get('validator'));
        }

        return $this->render(sprintf('SonataProductBundle:%s:view.html.twig', ucfirst($this->getProductCode($product))), array(
           'product' => $product,
           'form'    => $form,
        ));
    }

    public function renderFormBasketElementAction($fieldGroup, $basketElement)
    {

        return $this->render(sprintf('SonataProductBundle:%s:form_basket_element.html.twig', ucfirst($this->getProductCode($basketElement->getProduct()))), array(
           'fieldGroup'    => $fieldGroup,
           'basketElement' => $basketElement,
        ));
    }

    public function renderFinalReviewBasketElementAction($basketElement)
    {
        return $this->render(sprintf('SonataProductBundle:%s:final_review_basket_element.html.twig', ucfirst($this->getProductCode($basketElement->getProduct()))), array(
           'basketElement' => $basketElement,
        ));

    }

    public function getProductCode($product) {
        
        return $this->get('sonata.product.pool')->getProductCode($product);
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