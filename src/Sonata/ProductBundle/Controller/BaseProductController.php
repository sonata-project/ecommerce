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

use Application\PaymentBundle\Entity\Transaction;

abstract class BaseProductController extends Controller
{

    public function viewAction($product) {

        if(!is_object($product)) {
            throw new NotFoundHttpException('invalid product instance');
        }

        $form = $this->get('session')->getFlash('sonata.product.form');

        if(!$form) {
            $repository = $this->get('sonata.product.pool')->getRepository($product);

            $form = $repository->getAddBasketForm($product, $this->get('validator'));
        }

        return $this->render(sprintf('SonataProductBundle:%s:view.twig.html', ucfirst($this->getProductCode($product))), array(
           'product' => $product,
           'form'    => $form,
        ));
    }

    public function renderFormBasketElementAction($field_group, $basket_element)
    {

        return $this->render(sprintf('SonataProductBundle:%s:form_basket_element.twig.html', ucfirst($this->getProductCode($basket_element->getProduct()))), array(
           'field_group'    => $field_group,
           'basket_element' => $basket_element,
        ));
    }

    public function renderFinalReviewBasketElementAction($basket_element)
    {
        return $this->render(sprintf('SonataProductBundle:%s:final_review_basket_element.twig.html', ucfirst($this->getProductCode($basket_element->getProduct()))), array(
           'basket_element' => $basket_element,
        ));

    }

    public function getProductCode($product) {
        
        return $this->get('sonata.product.pool')->getProductCode($product);
    }


    public function viewVariationsAction($product_id, $slug) {

    }

    public function viewBasketElement($basket_element) {

    }

    public function viewBasketElementConfirmation($basket_element) {

    }

    public function viewOrderElement($order_element) {

    }

    public function viewEditOrderElement($order_element) {

    }
    
}