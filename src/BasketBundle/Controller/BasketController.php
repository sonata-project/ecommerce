<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\BasketBundle\Controller;

use Sonata\BasketBundle\Form\AddressType;
use Sonata\BasketBundle\Form\BasketType;
use Sonata\BasketBundle\Form\PaymentType;
use Sonata\BasketBundle\Form\ShippingType;
use Sonata\Component\Basket\BasketFactoryInterface;
use Sonata\Component\Customer\AddressInterface;
use Sonata\Component\Delivery\UndeliverableCountryException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Validator\ViolationMapper\ViolationMapper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @var BasketFactoryInterface
     */
    private $basketFactory;

    public function __construct(BasketFactoryInterface $basketFactory)
    {
        $this->basketFactory = $basketFactory;
    }

    /**
     * Shows the basket.
     *
     * @param Form $form
     *
     * @return Response
     */
    public function indexAction($form = null)
    {
        $form = $form ?: $this->createForm(BasketType::class, $this->get('sonata.basket'), [
            'validation_groups' => ['elements'],
        ]);

        // always validate the basket
        if (!$form->isSubmitted()) {
            if ($violations = $this->get('validator')->validate($form)) {
                $violationMapper = new ViolationMapper();
                foreach ($violations as $violation) {
                    $violationMapper->mapViolation($violation, $form, true);
                }
            }
        }

        $this->get('session')->set('sonata_basket_delivery_redirect', 'sonata_basket_delivery_address');

        $this->get('sonata.seo.page')->setTitle($this->get('translator')->trans('basket_index_title', [], 'SonataBasketBundle'));

        return $this->render('@SonataBasket/Basket/index.html.twig', [
            'basket' => $this->get('sonata.basket'),
            'form' => $form->createView(),
        ]);
    }

    /**
     * Update basket form rendering & saving.
     *
     * @return RedirectResponse|Response
     */
    public function updateAction(Request $request)
    {
        $form = $this->createForm(BasketType::class, $this->get('sonata.basket'), ['validation_groups' => ['elements']]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $basket = $form->getData();
            $basket->reset(false); // remove delivery and payment information
            $basket->clean(); // clean the basket

            // update the basket
            $this->basketFactory->save($basket);

            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        return $this->forward('SonataBasketBundle:Basket:index', [
            'form' => $form,
        ]);
    }

    /**
     * Adds a product to the basket.
     *
     * @throws MethodNotAllowedException
     * @throws NotFoundHttpException
     *
     * @return RedirectResponse|Response
     */
    public function addProductAction(Request $request)
    {
        $params = $request->get('add_basket');

        if ('POST' !== $request->getMethod()) {
            throw new MethodNotAllowedException(['POST']);
        }

        // retrieve the product
        $product = $this->get('sonata.product.set.manager')->findOneBy(['id' => $params['productId']]);

        if (!$product) {
            throw new NotFoundHttpException(sprintf('Unable to find the product with id=%d', $params['productId']));
        }

        // retrieve the custom provider for the product type
        $provider = $this->get('sonata.product.pool')->getProvider($product);

        $formBuilder = $this->get('form.factory')->createNamedBuilder('add_basket', FormType::class, null, [
            'data_class' => $this->container->getParameter('sonata.basket.basket_element.class'),
            'csrf_protection' => false,
        ]);

        $provider->defineAddBasketForm($product, $formBuilder);

        // load and bind the form
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        // if the form is valid add the product to the basket
        if ($form->isSubmitted() && $form->isValid()) {
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

            $this->basketFactory->save($basket);

            if ($request->isXmlHttpRequest() && $provider->getOption('product_add_modal')) {
                return $this->render('@SonataBasket/Basket/add_product_popin.html.twig', [
                    'basketElement' => $basketElement,
                    'locale' => $basket->getLocale(),
                    'product' => $product,
                    'price' => $price,
                    'currency' => $currency,
                    'quantity' => $quantity,
                    'provider' => $provider,
                ]);
            }

            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        // an error occur, forward the request to the view
        return $this->forward('SonataProductBundle:Product:view', [
            'productId' => $product,
            'slug' => $product->getSlug(),
        ]);
    }

    /**
     * Resets (empties) the basket.
     *
     * @return RedirectResponse
     */
    public function resetAction()
    {
        $this->basketFactory->reset($this->get('sonata.basket'));

        return new RedirectResponse($this->generateUrl('sonata_basket_index'));
    }

    /**
     * Displays a header preview of the basket.
     *
     * @return Response
     */
    public function headerPreviewAction()
    {
        return $this->render('@SonataBasket/Basket/header_preview.html.twig', [
            'basket' => $this->get('sonata.basket'),
        ]);
    }

    /**
     * Order process step 1: retrieve the customer associated with the logged in user if there's one
     * or create an empty customer and put it in the basket.
     *
     * @return RedirectResponse
     */
    public function authenticationStepAction()
    {
        $customer = $this->get('sonata.customer.selector')->get();

        $basket = $this->get('sonata.basket');
        $basket->setCustomer($customer);

        $this->basketFactory->save($basket);

        return new RedirectResponse($this->generateUrl('sonata_basket_delivery_address'));
    }

    /**
     * Order process step 5: choose payment mode.
     *
     * @throws HttpException
     *
     * @return RedirectResponse|Response
     */
    public function paymentStepAction(Request $request)
    {
        $basket = $this->get('sonata.basket');

        if (0 === $basket->countBasketElements()) {
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

        $form = $this->createForm(PaymentType::class, $basket, [
            'validation_groups' => ['delivery'],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // save the basket
            $this->basketFactory->save($basket);

            return new RedirectResponse($this->generateUrl('sonata_basket_final'));
        }

        $this->get('sonata.seo.page')->setTitle($this->get('translator')->trans('basket_payment_title', [], 'SonataBasketBundle'));

        return $this->render('@SonataBasket/Basket/payment_step.html.twig', [
            'basket' => $basket,
            'form' => $form->createView(),
            'customer' => $customer,
        ]);
    }

    /**
     * Order process step 3: choose delivery mode.
     *
     * @throws NotFoundHttpException
     *
     * @return RedirectResponse|Response
     */
    public function deliveryStepAction(Request $request)
    {
        $basket = $this->get('sonata.basket');

        if (0 === $basket->countBasketElements()) {
            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        $customer = $basket->getCustomer();

        if (!$customer) {
            throw new NotFoundHttpException('customer not found');
        }

        try {
            $form = $this->createForm(ShippingType::class, $basket, [
                'validation_groups' => ['delivery'],
            ]);
        } catch (UndeliverableCountryException $ex) {
            $countryName = Intl::getRegionBundle()->getCountryName($ex->getAddress()->getCountryCode());
            $message = $this->get('translator')->trans('undeliverable_country', ['%country%' => $countryName], 'SonataBasketBundle');
            $this->get('session')->getFlashBag()->add('error', $message);

            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        $template = '@SonataBasket/Basket/delivery_step.html.twig';

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // save the basket
            $this->basketFactory->save($form->getData());

            return new RedirectResponse($this->generateUrl('sonata_basket_payment_address'));
        }

        $this->get('sonata.seo.page')->setTitle($this->get('translator')->trans('basket_delivery_title', [], 'SonataBasketBundle'));

        return $this->render($template, [
            'basket' => $basket,
            'form' => $form->createView(),
            'customer' => $customer,
        ]);
    }

    /**
     * Order process step 2: choose an address from existing ones or create a new one.
     *
     * @throws NotFoundHttpException
     *
     * @return RedirectResponse|Response
     */
    public function deliveryAddressStepAction(Request $request)
    {
        $customer = $this->get('sonata.customer.selector')->get();

        if (!$customer) {
            throw new NotFoundHttpException('customer not found');
        }

        $basket = $this->get('sonata.basket');
        $basket->setCustomer($customer);

        if (0 === $basket->countBasketElements()) {
            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        $addresses = $customer->getAddressesByType(AddressInterface::TYPE_DELIVERY);

        $em = $this->container->get('sonata.address.manager')->getEntityManager();
        foreach ($addresses as $key => $address) {
            // Prevents usage of not persisted addresses in AddressType to avoid choice field error
            // This case occurs when customer is taken from a session
            if (!$em->contains($address)) {
                unset($addresses[$key]);
            }
        }

        // Show address creation / selection form
        $form = $this->createForm(AddressType::class, null, ['addresses' => $addresses]);
        $template = '@SonataBasket/Basket/delivery_address_step.html.twig';

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
            $this->basketFactory->save($basket);

            return new RedirectResponse($this->generateUrl('sonata_basket_delivery'));
        }

        // Set URL to be redirected to once edited address
        $this->get('session')->set('sonata_address_redirect', $this->generateUrl('sonata_basket_delivery_address'));

        $this->get('sonata.seo.page')->setTitle($this->get('translator')->trans('basket_delivery_title', [], 'SonataBasketBundle'));

        return $this->render($template, [
            'form' => $form->createView(),
            'addresses' => $addresses,
            'basket' => $basket,
        ]);
    }

    /**
     * Order process step 4: choose a billing address from existing ones or create a new one.
     *
     * @throws NotFoundHttpException
     *
     * @return RedirectResponse|Response
     */
    public function paymentAddressStepAction(Request $request)
    {
        $basket = $this->get('sonata.basket');

        if (0 === $basket->countBasketElements()) {
            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        $customer = $basket->getCustomer();

        if (!$customer) {
            throw new NotFoundHttpException('customer not found');
        }

        $addresses = $customer->getAddressesByType(AddressInterface::TYPE_BILLING);

        // Show address creation / selection form
        $form = $this->createForm(AddressType::class, null, ['addresses' => $addresses->toArray()]);
        $template = '@SonataBasket/Basket/payment_address_step.html.twig';

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
            $this->basketFactory->save($basket);

            return new RedirectResponse($this->generateUrl('sonata_basket_payment'));
        }

        // Set URL to be redirected to once edited address
        $this->get('session')->set('sonata_address_redirect', $this->generateUrl('sonata_basket_payment_address'));

        $this->get('sonata.seo.page')->setTitle($this->get('translator')->trans('basket_payment_title', [], 'SonataBasketBundle'));

        return $this->render($template, [
            'form' => $form->createView(),
            'addresses' => $addresses,
        ]);
    }

    /**
     * Order process step 6: order's review & conditions acceptance checkbox.
     *
     * @return RedirectResponse|Response
     */
    public function finalReviewStepAction(Request $request)
    {
        $basket = $this->get('sonata.basket');

        $violations = $this
            ->get('validator')
            ->validate($basket, null, ['elements', 'delivery', 'payment']);

        if ($violations->count() > 0) {
            // basket not valid

            // todo : add flash message rendering in template
            foreach ($violations as $violation) {
                $this->get('session')->getFlashBag()->add('error', 'Error: '.$violation->getMessage());
            }

            return new RedirectResponse($this->generateUrl('sonata_basket_index'));
        }

        if ('POST' === $request->getMethod()) {
            if ($request->get('tac')) {
                // send the basket to the payment callback
                return $this->forward('SonataPaymentBundle:Payment:sendbank');
            }
        }

        $this->get('sonata.seo.page')->setTitle($this->get('translator')->trans('basket_review_title', [], 'SonataBasketBundle'));

        return $this->render('@SonataBasket/Basket/final_review_step.html.twig', [
            'basket' => $basket,
            'tac_error' => 'POST' === $request->getMethod(),
        ]);
    }
}
