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


use Symfony\Component\Form\Form;
use Symfony\Component\Form\FieldGroup;
use Symfony\Component\Form\ChoiceField;
use Symfony\Component\Form\CollectionField;
use Symfony\Bundle\DoctrineBundle\Form\ValueTransformer\CollectionToChoiceTransformer;
use Symfony\Bundle\DoctrineBundle\Form\ValueTransformer\EntityToIDTransformer;

use Sonata\Component\Form\Transformer\DeliveryMethodTransformer;
use Sonata\Component\Form\Transformer\PaymentMethodTransformer;

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
        $form = new Form('basket', clone $this->get('sonata.basket'), $this->get('validator'), array('validation_groups' => 'elements'));

        $elements = new FieldGroup('basketElements');
        
        foreach($this->get('sonata.basket')->getBasketElements() as $basket_element) {

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

        $form = $form ?: $this->getBasketForm();

        // always validate the basket
        if(!$form->isBound())
        {
            // todo : move this somewhere else
            if ($violations = $this->get('validator')->validate($form, $form->getValidationGroups())) {

                foreach ($violations as $violation) {
                    $propertyPath = new \Symfony\Component\Form\PropertyPath($violation->getPropertyPath());
                    $iterator = $propertyPath->getIterator();

                    if ($iterator->current() == 'data') {
                        $type = \Symfony\Component\Form\Form::DATA_ERROR;
                        $iterator->next(); // point at the first data element
                    } else {
                        $type = \Symfony\Component\Form\Form::FIELD_ERROR;
                    }

                    $form->addError(new \Symfony\Component\Form\FieldError($violation->getMessageTemplate(), $violation->getMessageParameters()), $iterator, $type);
                }
            }
        }

        return $this->render('SonataBasketBundle:Basket:index.twig.html', array(
            'basket' => $this->get('sonata.basket'),
            'form'   => $form
        ));
    }

    public function updateAction()
    {
        $params = $this->get('request')->get('basket');

        $form = $this->getBasketForm();
        $form->bind($params);

        if($form->isValid()) {

            $basket = $form->getData();
            $basket->reset(false); // remove delivery and payment information
            $basket->clean(); // clean the basket

            // update the basket store in session
            $this->get('session')->set('sonata/basket', $basket);

            return $this->redirect($this->generateUrl('sonata_basket_index'));
        }

        return $this->forward('SonataBasketBundle:Basket:index', array(
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
            ->find('Application\Sonata\ProductBundle\Entity\Product', $params['product_id']);

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
        return $this->forward('SonataProductBundle:Product:view', array(
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

//        throw new \Exception();
        return $this->render('SonataBasketBundle:Basket:header_preview.twig.html', array(
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

        return $this->redirect($this->generateUrl('sonata_basket_delivery'));
    }

    public function getPaymentForm($basket, $payment_addresses, $payment_methods)
    {
        $form = new Form('payment', $basket, $this->get('validator'), array(
            'validation_groups' => 'payment'
        ));

        $transformer = new EntityToIDTransformer(array(
            'em' =>  $this->get('doctrine.orm.default_entity_manager'),
            'className' => 'Application\Sonata\BasketBundle\Entity\Address'
        ));

        $field = new ChoiceField('payment_address', array(
            'expanded' => true,
            'choices' => array_map(function($address)  { return $address->getFullAddress('<br />'); }, $payment_addresses),
            'value_transformer' => $transformer,
        ));
        $form->add($field);

        $choices = array();

        foreach($payment_methods as $method) {
            $choices[$method->getCode()] = $method->getName();
        }

        $field = new ChoiceField('payment_method', array(
            'expanded' => true,
            'choices' => $choices,
            'value_transformer' => new PaymentMethodTransformer(array(
                'payment_pool' => $this->get('sonata.payment.pool')
            )),
        ));

        $form->add($field);

        return $form;
    }
    
    public function paymentStepAction()
    {
        $basket = clone $this->get('sonata.basket');

        if($basket->countBasketElements() == 0) {

            return $this->redirect($this->generateUrl('sonata_basket_index'));
        }

        $user = $this->container->get('doctrine_user.auth')->getUser();

        if(!$user) {
            throw new HttpException('Invalid user');
        }

        $payment_addresses = $this
            ->get('doctrine.orm.default_entity_manager')
            ->createQuery('SELECT a FROM Application\Sonata\BasketBundle\Entity\Address a INDEX BY a.id WHERE a.type = :type AND a.user = :user')
            ->setParameters(array(
                'type' => \Application\BasketBundle\Entity\Address::TYPE_BILLING,
                'user' => $user->getId())
            )
            ->execute();

        $payment_address = $basket->getPaymentAddress() ?: current($payment_addresses);
        $basket->setPaymentAddress($payment_address);

        $payment_methods = $this
            ->get('sonata.payment.selector')
            ->getAvailableMethods($basket, $payment_address);

        if($payment_methods === false) {

            // something went wrong while selecting
            // redirect the user to the basket index (validation)
            return $this->redirect($this->generateUrl('sonata_basket_index'));
        }

        $form = $this->getPaymentForm($basket, $payment_addresses, $payment_methods);

        if($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request')->get('payment'));

            if($form->isValid()) {

                // update the basket store in session
                $this->get('session')->set('sonata/basket', $form->getData());

                return $this->redirect($this->generateUrl('sonata_basket_final'));
            }
        }

        return $this->render('SonataBasketBundle:Basket:payment_step.twig.html', array(
            'basket' => $basket,
            'form'   => $form,
            'user'   => $user,
            'payment_methods' => $payment_methods
        ));
    }

    public function getDeliveryForm($basket, $delivery_addresses, $delivery_methods)
    {
        $form = new Form('shipping', $basket, $this->get('validator'), array('validation_groups' => 'delivery'));

        $transformer = new EntityToIDTransformer(array(
            'em' =>  $this->get('doctrine.orm.default_entity_manager'),
            'className' => 'Application\Sonata\BasketBundle\Entity\Address'
        ));

        $field = new ChoiceField('deliveryAddress', array(
            'expanded' => true,
            'choices' => array_map(function($address)  { return $address->getFullAddress('<br />'); }, $delivery_addresses),
            'value_transformer' => $transformer,
        ));
        $form->add($field);

        $choices = array();

        foreach($delivery_methods as $method) {
            $choices[$method->getCode()] = $method->getName();
        }

        $field = new ChoiceField('deliveryMethod', array(
            'expanded' => true,
            'choices' => $choices,
            'value_transformer' => new DeliveryMethodTransformer(array(
                'delivery_pool' => $this->get('sonata.delivery.pool')
            )),
        ));
        
        $form->add($field);

        return $form;
    }

    public function deliveryStepAction()
    {

        $basket = clone $this->get('sonata.basket');

        if($basket->countBasketElements() == 0) {

            return $this->redirect($this->generateUrl('sonata_basket_index'));
        }

        $customer = $basket->getCustomer();

        if(!$customer) {
            throw new NotFoundHttpException('customer not found');
        }

        $delivery_addresses = $this
            ->get('doctrine.orm.default_entity_manager')
            ->createQuery('SELECT a FROM Application\Sonata\CustomerBundle\Entity\Address a INDEX BY a.id WHERE a.type = :type AND a.customer = :customer')
            ->setParameters(array(
                'type' => \Application\Sonata\CustomerBundle\Entity\Address::TYPE_DELIVERY,
                'customer' => $customer->getId())
            )
            ->execute();

        $delivery_address = $basket->getDeliveryAddress() ?: current($delivery_addresses);

        $basket->setDeliveryAddress($delivery_address);
        
        $delivery_methods = $this
            ->get('sonata.delivery.selector')
            ->getAvailableMethods($basket, $delivery_address);
        
        if($delivery_methods === false) {

            // something went wrong while selecting
            // redirect the user to the basket index (validation)
            return $this->redirect($this->generateUrl('sonata_basket_index'));
        }

        $form = $this->getDeliveryForm($basket, $delivery_addresses, $delivery_methods);

        if($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request')->get('shipping'));

            if($form->isValid()) {

                // update the basket store in session
                $this->get('session')->set('sonata/basket', $form->getData());

                return $this->redirect($this->generateUrl('sonata_basket_payment'));
            }
        }

        return $this->render('SonataBasketBundle:Basket:delivery_step.twig.html', array(
            'basket' => $basket,
            'form'   => $form,
            'customer'   => $customer,
            'delivery_methods' => $delivery_methods
        ));
    }

    public function finalReviewStepAction()
    {

        $basket = $this->get('sonata.basket');
        
        if($this->get('request')->getMethod() == 'POST' ) {

            $violations = $this->get('validator')->validate($basket, array('elements', 'delivery', 'payment'));
            if ($violations->count() > 0) {
                // basket not valid

                // todo : add flash message

                return $this->redirect($this->generateUrl('sonata_basket_index'));
            }

            if($this->get('request')->get('tac'))
            {
                // send the basket to the payment callback
                return $this->forward('SonataPaymentBundle:Payment:callbank');
            }

        }

        return $this->render('SonataBasketBundle:Basket:final_review_step.twig.html', array(
            'basket'    => $basket,
            'tac_error' => $this->get('request')->getMethod() == 'POST'
        ));
    }
}
