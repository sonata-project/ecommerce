<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\BasketBundle\Controller;

use Sonata\Component\Customer\AddressInterface;
use Sonata\Component\Delivery\UndeliverableCountryException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\Form\Extension\Validator\ViolationMapper\ViolationMapper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

/**
 * This controller manages the Basket operation and most of the order process.
 */
class BasketController extends Controller
{
    /**
     * Shows the basket.
     *
     * @param Form $form
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($form = null)
    {
        $form = $form ?: $this->createForm('sonata_basket_basket', clone $this->get('sonata.basket'), array(
            'validation_groups' => array('elements'),
        ));

        // always validate the basket
        if (!$form->isBound()) {
            if ($violations = $this->get('validator')->validate($form)) {
                $violationMapper = new ViolationMapper();
                foreach ($violations as $violation) {
                    $violationMapper->mapViolation($violation, $form, true);
                }
            }
        }

        $this->get('session')->set('sonata_basket_delivery_redirect', 'sonata_basket_delivery_address');

        $this->get('sonata.seo.page')->setTitle($this->get('translator')->trans('basket_index_title', array(), 'SonataBasketBundle'));

        return $this->render('SonataBasketBundle:Basket:index.html.twig', array(
            'basket' => $this->get('sonata.basket'),
            'form'   => $form->createView(),
        ));
    }

    /**
     * Update basket form rendering & saving.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateAction()
    {
        $form = $this->createForm('sonata_basket_basket', clone $this->get('sonata.basket'), array('validation_groups' => array('elements')));
        $form->bind($this->get('request'));

        if ($form->isValid()) {
            $basket = $form->getData();
            $basket->reset(false); // remove delivery and payment information
            $basket->clean(); // clean the basket

            // update the basket
            $this->get('sonata.basket.factory')->save($basket);

            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        return $this->forward('SonataBasketBundle:Basket:index', array(
            'form' => $form,
        ));
    }

    /**
     * Adds a product to the basket.
     *
     * @throws MethodNotAllowedException
     * @throws NotFoundHttpException
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addProductAction()
    {
        $request = $this->get('request');
        $params  = $request->get('add_basket');

        if ($request->getMethod() != 'POST') {
            throw new MethodNotAllowedException(array('POST'));
        }

        // retrieve the product
        $product = $this->get('sonata.product.set.manager')->findOneBy(array('id' => $params['productId']));

        if (!$product) {
            throw new NotFoundHttpException(sprintf('Unable to find the product with id=%d', $params['productId']));
        }

        // retrieve the custom provider for the product type
        $provider = $this->get('sonata.product.pool')->getProvider($product);

        $formBuilder = $this->get('form.factory')->createNamedBuilder('add_basket', 'form', null, array('data_class' => $this->container->getParameter('sonata.basket.basket_element.class'), 'csrf_protection' => false));
        $provider->defineAddBasketForm($product, $formBuilder);

        // load and bind the form
        $form = $formBuilder->getForm();
        $form->bind($request);

        // if the form is valid add the product to the basket
        if ($form->isValid()) {
            $basket = $this->get('sonata.basket');
            $basketElement = $form->getData();

            $quantity = $basketElement->getQuantity();
            $currency = $this->get('sonata.basket')->getCurrency();
            $price = $provider->calculatePrice($product, $currency, true, $quantity);

            if ($basket->hasProduct($product)) {
                $basketElement = $provider->basketMergeProduct($basket, $product, $basketElement);
            } else {
                $basketElement = $provider->basketAddProduct($basket, $product, $basketElement);
            }

            if ($request->isXmlHttpRequest() && $provider->getOption('product_add_modal')) {
                return $this->render('SonataBasketBundle:Basket:add_product_popin.html.twig', array(
                    'basketElement' => $basketElement,
                    'locale'        => $basket->getLocale(),
                    'product'       => $product,
                    'price'         => $price,
                    'currency'      => $currency,
                    'quantity'      => $quantity,
                    'provider'      => $provider,
                ));
            } else {
                return new RedirectResponse($this->generateUrl('sonata_basket_index'));
            }
        }

        // an error occur, forward the request to the view
        return $this->forward('SonataProductBundle:Product:view', array(
            'productId' => $product,
            'slug'      => $product->getSlug(),
        ));
    }

    /**
     * Resets (empties) the basket.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function resetAction()
    {
        $this->get('sonata.basket')->reset();

        return new RedirectResponse($this->generateUrl('sonata_basket_index'));
    }

    /**
     * Displays a header preview of the basket.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function headerPreviewAction()
    {
        return $this->render('SonataBasketBundle:Basket:header_preview.html.twig', array(
            'basket' => $this->get('sonata.basket'),
        ));
    }

    /**
     * Order process step 1: retrieve the customer associated with the logged in user if there's one
     * or create an empty customer and put it in the basket.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function authenticationStepAction()
    {
        $customer = $this->get('sonata.customer.selector')->get();

        $basket = $this->get('sonata.basket');
        $basket->setCustomer($customer);

        $this->get('sonata.basket.factory')->save($basket);

        return new RedirectResponse($this->generateUrl('sonata_basket_delivery_address'));
    }

    /**
     * Order process step 5: choose payment mode.
     *
     * @throws HttpException
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
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

        if (null === $basket->getBillingAddress()) {
            // If no payment address is specified, we assume it's the same as the delivery
            $billingAddress = clone $basket->getDeliveryAddress();
            $billingAddress->setType(AddressInterface::TYPE_BILLING);
            $basket->setBillingAddress($billingAddress);
        }

        $form = $this->createForm('sonata_basket_payment', $basket, array(
            'validation_groups' => array('delivery'),
        ));

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            if ($form->isValid()) {
                // save the basket
                $this->get('sonata.basket.factory')->save($basket);

                return new RedirectResponse($this->generateUrl('sonata_basket_final'));
            }
        }

        $this->get('sonata.seo.page')->setTitle($this->get('translator')->trans('basket_payment_title', array(), 'SonataBasketBundle'));

        return $this->render('SonataBasketBundle:Basket:payment_step.html.twig', array(
            'basket'     => $basket,
            'form'       => $form->createView(),
            'customer'   => $customer,
        ));
    }

    /**
     * Order process step 3: choose delivery mode.
     *
     * @throws NotFoundHttpException
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
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

        try {
            $form = $this->createForm('sonata_basket_shipping', $basket, array(
                'validation_groups' => array('delivery'),
            ));
        } catch (UndeliverableCountryException $ex) {
            $countryName = Intl::getRegionBundle()->getCountryName($ex->getAddress()->getCountryCode());
            $message = $this->get('translator')->trans('undeliverable_country', array('%country%' => $countryName), 'SonataBasketBundle');
            $this->get('session')->getFlashBag()->add('error', $message);

            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        $template = 'SonataBasketBundle:Basket:delivery_step.html.twig';

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            if ($form->isValid()) {
                // save the basket
                $this->get('sonata.basket.factory')->save($form->getData());

                return new RedirectResponse($this->generateUrl('sonata_basket_payment_address'));
            }
        }

        $this->get('sonata.seo.page')->setTitle($this->get('translator')->trans('basket_delivery_title', array(), 'SonataBasketBundle'));

        return $this->render($template, array(
            'basket'   => $basket,
            'form'     => $form->createView(),
            'customer' => $customer,
        ));
    }

    /**
     * Order process step 2: choose an address from existing ones or create a new one.
     *
     * @throws NotFoundHttpException
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deliveryAddressStepAction()
    {
        $customer = $this->get('sonata.customer.selector')->get();

        if (!$customer) {
            throw new NotFoundHttpException('customer not found');
        }

        $basket = $this->get('sonata.basket');
        $basket->setCustomer($customer);

        if ($basket->countBasketElements() == 0) {
            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        $addresses = $customer->getAddressesByType(AddressInterface::TYPE_DELIVERY);

        // Show address creation / selection form
        $form = $this->createForm('sonata_basket_address', null, array('addresses' => $addresses->toArray()));
        $template = 'SonataBasketBundle:Basket:delivery_address_step.html.twig';

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            if ($form->isValid()) {
                if ($form->has('useSelected') && $form->get('useSelected')->isClicked()) {
                    $address = $form->get('addresses')->getData();
                } else {
                    $address = $form->getData();
                    $address->setType(AddressInterface::TYPE_DELIVERY);

                    $customer->addAddress($address);

                    $this->get('sonata.customer.manager')->save($customer);

                    $this->get('session')->getFlashBag()->add('sonata_customer_success', 'address_add_success');
                }

                $basket->setCustomer($customer);
                $basket->setDeliveryAddress($address);
                // save the basket
                $this->get('sonata.basket.factory')->save($basket);

                return new RedirectResponse($this->generateUrl('sonata_basket_delivery'));
            }
        }

        // Set URL to be redirected to once edited address
        $this->get('session')->set('sonata_address_redirect', $this->generateUrl('sonata_basket_delivery_address'));

        $this->get('sonata.seo.page')->setTitle($this->get('translator')->trans('basket_delivery_title', array(), 'SonataBasketBundle'));

        return $this->render($template, array(
            'form'      => $form->createView(),
            'addresses' => $addresses,
        ));
    }

    /**
     * Order process step 4: choose a billing address from existing ones or create a new one.
     *
     * @throws NotFoundHttpException
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function paymentAddressStepAction()
    {
        $basket = $this->get('sonata.basket');

        if ($basket->countBasketElements() == 0) {
            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        $customer = $basket->getCustomer();

        if (!$customer) {
            throw new NotFoundHttpException('customer not found');
        }

        $addresses = $customer->getAddressesByType(AddressInterface::TYPE_BILLING);

        // Show address creation / selection form
        $form = $this->createForm('sonata_basket_address', null, array('addresses' => $addresses->toArray()));
        $template = 'SonataBasketBundle:Basket:payment_address_step.html.twig';

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            if ($form->isValid()) {
                if ($form->has('useSelected') && $form->get('useSelected')->isClicked()) {
                    $address = $form->get('addresses')->getData();
                } else {
                    $address = $form->getData();
                    $address->setType(AddressInterface::TYPE_BILLING);

                    $customer->addAddress($address);

                    $this->get('sonata.customer.manager')->save($customer);

                    $this->get('session')->getFlashBag()->add('sonata_customer_success', 'address_add_success');
                }

                $basket->setCustomer($customer);
                $basket->setBillingAddress($address);
                // save the basket
                $this->get('sonata.basket.factory')->save($basket);

                return new RedirectResponse($this->generateUrl('sonata_basket_payment'));
            }
        }

        // Set URL to be redirected to once edited address
        $this->get('session')->set('sonata_address_redirect', $this->generateUrl('sonata_basket_payment_address'));

        $this->get('sonata.seo.page')->setTitle($this->get('translator')->trans('basket_payment_title', array(), 'SonataBasketBundle'));

        return $this->render($template, array(
            'form'      => $form->createView(),
            'addresses' => $addresses,
        ));
    }

    /**
     * Order process step 6: order's review & conditions acceptance checkbox.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function finalReviewStepAction()
    {
        $basket = $this->get('sonata.basket');

        $violations = $this
            ->get('validator')
            ->validate($basket, array('elements', 'delivery', 'payment'));

        if ($violations->count() > 0) {
            // basket not valid

            // todo : add flash message rendering in template
            foreach ($violations as $violation) {
                $this->get('session')->getFlashBag()->add('error', 'Error: '.$violation->getMessage());
            }

            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        if ($this->get('request')->getMethod() == 'POST') {
            if ($this->get('request')->get('tac')) {
                // send the basket to the payment callback
                return $this->forward('SonataPaymentBundle:Payment:sendbank');
            }
        }

        $this->get('sonata.seo.page')->setTitle($this->get('translator')->trans('basket_review_title', array(), 'SonataBasketBundle'));

        return $this->render('SonataBasketBundle:Basket:final_review_step.html.twig', array(
            'basket'    => $basket,
            'tac_error' => $this->get('request')->getMethod() == 'POST',
        ));
    }
}
