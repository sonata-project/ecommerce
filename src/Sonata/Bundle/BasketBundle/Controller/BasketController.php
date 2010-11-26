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

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FieldGroup;


class BasketController extends Controller
{

    /**
     * return the basket form
     *
     * @return Symfony\Component\Form\Form
     */
    public function getBasketForm()
    {

        // always clone the basket, so the one in session is never altered
        $form = new Form('basket', clone $this->get('sonata.basket'), $this->get('validator'));
        $elements = new FieldGroup('elements');
        
        foreach($this->get('sonata.basket')->getElements() as $basket_element) {

            // ask each product repository to populate an empty group field instance
            // so each line can be tweaked depends on the product logic
            $field_group = $this
                ->get('sonata.product.pool')
                ->getRepository($basket_element->getProduct())
                ->generateFieldGroupBasketElement(
                    new FieldGroup($basket_element->getPos()),
                    $basket_element
                );

            $elements->add($field_group);
        }

        $form->add($elements);
        
        return $form;
    }
    
    public function indexAction($form = null)
    {
        // make sure the session is enabled
        $this->get('session')->start();
        
        return $this->render('BasketBundle:Basket:index.twig', array(
            'basket' => $this->get('sonata.basket'),
            'form'   => $form ? $form : $this->getBasketForm()
        ));
    }

    public function updateAction()
    {
        $params = $this->get('request')->get('basket');

        $form = $this->getBasketForm();
        $form->bind($params);

        if($form->isValid()) {

            $basket = $form->getData();
            $basket->clean(); // clean the basket

            // update the basket store in session
            $this->get('session')->set('sonata/basket', $basket);

            return $this->redirect($this->generateUrl('sonata_basket_index'));
        }

        return $this->forward('BasketBundle:Basket:index', array(
           'form' => $form
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

    public function resetAction()
    {
        $this->get('sonata.basket')->reset();

        return $this->redirect($this->generateUrl('sonata_basket_index'));
    }

    public function headerPreviewAction()
    {

        return $this->render('BasketBundle:Basket:header_preview.twig', array(
             'basket' => $this->get('sonata.basket')
        ));
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
