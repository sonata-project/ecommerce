<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Sonata\Component\Payment\Transaction;

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

        return $this->render(sprintf('ProductBundle:%s:view.twig', ucfirst($this->getProductCode($product))), array(
           'product' => $product,
           'form'    => $form,
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