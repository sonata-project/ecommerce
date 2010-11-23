<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\BasketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BasketController extends Controller
{

    public function indexAction()
    {
        return $this->render('BasketBundle:Basket:index.twig', array(
            'basket' => $this->get('sonata.basket'),
        ));
    }

    public function addProductAction()
    {
        $request = $this->get('request');

        // start the session
        $this->get('session')->start();

        if($request->getMethod() != 'POST') {
            throw new ForbiddenHttpException('invalid request');
        }

        $params = $request->get('basket');

        // retrieve the product
        $product = $this
            ->get('doctrine.orm.default_entity_manager')
            ->find('ProductBundle:Product', $params['product_id']);

        if(!$product) {
            throw new NotFoundHttpException(sprintf('Unable to find the product with id=%d', $params['product_id']));
        }

        // retrieve the custom repository for the product type
        $repository = $this->get('sonata.product.pool')->getRepository($product);

        // load and bind the form
        $form = $repository->getAddBasketForm($product, $this->get('validator'));
        $form->bind($params);

        // if the form is valid add the product to the basket
        if($form->isValid()) {

            $basket = $this->get('sonata.basket');

            if($basket->hasProduct($product)) {
                $repository->basketMergeProduct($basket,  $product, $form->getData());
            } else {
                $repository->basketAddProduct($basket,  $product, $form->getData());
            }

            return $this->redirect($this->generateUrl('sonata_basket_index'));
        }
        
        // an error occur, forward the request to the view
        return $this->forward('ProductBundle:Product:view', array(
            'product_id' => $product,
            'slug'       => $product->getSlug(),
        ));
    }

    public function updateAction()
    {

    }

    public function resetAction()
    {

    }

    public function validationStepAction()
    {

    }

    public function authentificationStepAction()
    {

    }

    public function billingStepAction()
    {

    }

    public function shippingStepAction()
    {

    }

    public function finalReviewStepAction()
    {

    }
}
