<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\BasketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Sonata\Component\Form\Transformer\PaymentMethodTransformer;
use Sonata\Component\Basket\InvalidBasketStateException;

class BasketController extends Controller
{
    public function indexAction($form = null)
    {
        $form = $form ?: $this->createForm('sonata_basket_basket', clone $this->get('sonata.basket'), array(
            'validation_groups' => array('elements')
        ));

        // always validate the basket
        if (!$form->isBound()) {
            // todo : move this somewhere else
//            if ($violations = $this->get('validator')->validate($form->getDa, $form->getValidationGroups())) {
//
//                foreach ($violations as $violation) {
//                    $propertyPath = new \Symfony\Component\PropertyAccess\PropertyPath($violation->getPropertyPath());
//                    $iterator = $propertyPath->getIterator();
//
//                    if ($iterator->current() == 'data') {
//                        $type = \Symfony\Component\Form\Form::DATA_ERROR;
//                        $iterator->next(); // point at the first data element
//                    } else {
//                        $type = \Symfony\Component\Form\Form::FIELD_ERROR;
//                    }
//
//                    $form->addError(new \Symfony\Component\Form\FieldError($violation->getMessageTemplate(), $violation->getMessageParameters()), $iterator, $type);
//                    // WARNING: ConstraintValidator::getMessageParameters() and ConstraintValidator::getMessageTemplate() has been removed in 2.3 (see https://github.com/symfony/symfony/blob/master/UPGRADE-2.1.md#validator)
//                }
//            }
        }

        return $this->render('SonataBasketBundle:Basket:index.html.twig', array(
            'basket' => $this->get('sonata.basket'),
            'form'   => $form->createView(),
        ));
    }

    public function updateAction()
    {
        $form = $this->createForm('sonata_basket_basket', clone $this->get('sonata.basket'), array('validation_groups' => array('elements')));
        $form->bind($this->get('request'));

        if ($form->isValid()) {
            $basket = $form->getData();
            $basket->reset(false); // remove delivery and payment information
            $basket->clean(); // clean the basket

            // update the basket stored in session
            $this->get('session')->set('sonata/basket', $basket);

            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        return $this->forward('SonataBasketBundle:Basket:index', array(
           'form' => $form
        ));
    }

    public function addProductAction()
    {
        $request = $this->get('request');
        $params  = $request->get('add_basket');

        if ($request->getMethod() != 'POST') {
            throw new MethodNotAllowedHttpException('POST');
        }

        // retrieve the product
        $product = $this->get('sonata.product.collection.manager')->findOneBy(array('id' => $params['productId']));

        if (!$product) {
            throw new NotFoundHttpException(sprintf('Unable to find the product with id=%d', $params['productId']));
        }

        // retrieve the custom provider for the product type
        $provider = $this->get('sonata.product.pool')->getProvider($product);

        $formBuilder = $this->get('form.factory')->createNamedBuilder('form', 'add_basket');
        $provider->defineAddBasketForm($product, $formBuilder);

        // load and bind the form
        $form = $formBuilder->getForm();
        $form->bind($request);

        // if the form is valid add the product to the basket
        if ($form->isValid()) {
            $basket = $this->get('sonata.basket');

            if ($basket->hasProduct($product)) {
                $provider->basketMergeProduct($basket,  $product, $form->getData());
            } else {
                $provider->basketAddProduct($basket,  $product, $form->getData());
            }

            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        // an error occur, forward the request to the view
        return $this->forward('SonataProductBundle:Product:view', array(
            'productId' => $product,
            'slug'       => $product->getSlug(),
        ));
    }

    public function resetAction()
    {
        $this->get('sonata.basket')->reset();

        return new RedirectResponse($this->generateUrl('sonata_basket_index'));
    }

    public function headerPreviewAction()
    {
        return $this->render('SonataBasketBundle:Basket:header_preview.html.twig', array(
             'basket' => $this->get('sonata.basket')
        ));
    }

    public function authentificationStepAction()
    {
        // todo : code the connection bit
        $customers = $this
            ->get('doctrine.orm.default_entity_manager')
            ->createQuery('SELECT c FROM Application\Sonata\CustomerBundle\Entity\Customer c')
            ->setMaxResults(1)
            ->execute();

        $this->get('sonata.basket')->setCustomer(count($customers) > 0 ? $customers[0] : null);

        return new RedirectResponse($this->generateUrl('sonata_basket_delivery'));
    }

    public function paymentStepAction()
    {
        $basket = clone $this->get('sonata.basket');

        if ($basket->countBasketElements() == 0) {
            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        $customer = $basket->getCustomer();

        if (!$customer) {
            throw new HttpException('Invalid customer');
        }

        $form = $this->createForm('sonata_basket_payment', $basket, array(
            'validation_groups' => array('delivery')
        ));

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            if ($form->isValid()) {
                // update the basket store in session
                $this->get('session')->set('sonata/basket', $form->getData());

                return new RedirectResponse($this->generateUrl('sonata_basket_final'));
            }
        }

        return $this->render('SonataBasketBundle:Basket:payment_step.html.twig', array(
            'basket' => $basket,
            'form'   => $form->createView(),
            'customer'   => $customer,
        ));
    }

    public function deliveryStepAction()
    {
        $basket = clone $this->get('sonata.basket');

        if ($basket->countBasketElements() == 0) {
            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        $customer = $basket->getCustomer();

        if (!$customer) {
            throw new NotFoundHttpException('customer not found');
        }

        $form = $this->createForm('sonata_basket_shipping', $basket, array(
            'validation_groups' => array('delivery')
        ));

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            if ($form->isValid()) {
                // update the basket store in session
                $this->get('session')->set('sonata/basket', $form->getData());

                return new RedirectResponse($this->generateUrl('sonata_basket_payment'));
            }
        }

        return $this->render('SonataBasketBundle:Basket:delivery_step.html.twig', array(
            'basket'   => $basket,
            'form'     => $form->createView(),
            'customer' => $customer,
        ));
    }

    public function finalReviewStepAction()
    {
        $basket = $this->get('sonata.basket');

        $violations = $this
            ->get('validator')
            ->validate($basket, array('elements', 'delivery', 'payment'));

        if ($violations->count() > 0) {
            // basket not valid

            // todo : add flash message

            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        if ($this->get('request')->getMethod() == 'POST' ) {
            if ($this->get('request')->get('tac')) {
                // send the basket to the payment callback
                return $this->forward('SonataPaymentBundle:Payment:callbank');
            }
        }

        return $this->render('SonataBasketBundle:Basket:final_review_step.html.twig', array(
            'basket'    => $basket,
            'tac_error' => $this->get('request')->getMethod() == 'POST'
        ));
    }
}
