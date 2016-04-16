<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Controller;

use Sonata\Component\Basket\BasketElementInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Product\ProductInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class BaseProductController extends Controller
{
    /**
     * @param $product
     *
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function viewAction($product)
    {
        if (!is_object($product)) {
            throw new NotFoundHttpException('invalid product instance');
        }

        $provider = $this->get('sonata.product.pool')->getProvider($product);

        $formBuilder = $this->get('form.factory')->createNamedBuilder('add_basket', 'form', null, array('data_class' => $this->container->getParameter('sonata.basket.basket_element.class'), 'csrf_protection' => false));
        $provider->defineAddBasketForm($product, $formBuilder);

        $form = $formBuilder->getForm()->createView();

        $crossSellingProducts = $this->get('sonata.product.finder')->getCrossSellingSimilarParentProducts($product, 4);
        $currency = $this->get('sonata.price.currency.detector')->getCurrency();

        // Add twitter/FB metadata
        $this->updateSeoMeta($product, $currency);

        return $this->render(
            sprintf('%s:view.html.twig', $provider->getBaseControllerName()),
            array(
                'provider'           => $provider,
                'product'            => $product,
                'cheapest_variation' => $provider->getCheapestEnabledVariation($product),
                'currency'           => $currency,
                'similar_cross'      => $crossSellingProducts,
                'form'               => $form,
            )
        );
    }

    /**
     * Renders product properties.
     *
     * @param ProductInterface $product
     *
     * @return Response
     */
    public function renderPropertiesAction(ProductInterface $product)
    {
        $provider = $this->get('sonata.product.pool')->getProvider($product);

        return $this->render(sprintf('%s:properties.html.twig', $provider->getBaseControllerName()), array(
            'product' => $product,
        ));
    }

    /**
     * @param FormView               $formView
     * @param BasketElementInterface $basketElement
     * @param BasketInterface        $basket
     *
     * @return Response
     */
    public function renderFormBasketElementAction(FormView $formView, BasketElementInterface $basketElement, BasketInterface $basket)
    {
        $provider = $this->get('sonata.product.pool')->getProvider($basketElement->getProduct());

        return $this->render(sprintf('%s:form_basket_element.html.twig', $provider->getBaseControllerName()), array(
            'formView'      => $formView,
            'basketElement' => $basketElement,
            'basket'        => $basket,
        ));
    }

    /**
     * @param BasketElementInterface $basketElement
     * @param BasketInterface        $basket
     *
     * @return Response
     */
    public function renderFinalReviewBasketElementAction(BasketElementInterface $basketElement, BasketInterface $basket)
    {
        $provider = $this->get('sonata.product.pool')->getProvider($basketElement->getProduct());

        return $this->render(sprintf('%s:final_review_basket_element.html.twig', $provider->getBaseControllerName()), array(
            'basketElement' => $basketElement,
            'basket'        => $basket,
        ));
    }

    /**
     * @param $product
     *
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function viewVariationsAction($product)
    {
        if (!is_object($product)) {
            throw new NotFoundHttpException('invalid product instance');
        }

        $provider = $this->get('sonata.product.pool')->getProvider($product);

        return $this->render(sprintf('%s:view_variations.html.twig', $provider->getBaseControllerName()), array(
            'product' => $product,
        ));
    }

    /**
     * @param ProductInterface      $product
     * @param ProductInterface|null $variation
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function variationToProductAction(ProductInterface $product, ProductInterface $variation = null)
    {
        $provider = $this->get('sonata.product.pool')->getProvider($product);

        if (!$provider->hasEnabledVariations($product)) {
            throw new NotFoundHttpException('invalid product instance (no variations)');
        }

        if (null === $variation || 0 === $provider->getStockAvailable($variation)) {
            if ($this->getRequest()->isXmlHttpRequest()) {
                return new JsonResponse(array('error' => $this->get('translator')->trans('variation_not_found', array(), 'SonataProductBundle')));
            }

            $this->get('session')->getFlashBag()->add('sonata_product_error', 'variation_not_found');

            // Go to master product
            $variation = $product;
        }

        $url = $this->generateUrl('sonata_product_view', array(
            'productId' => $variation->getId(),
            'slug'      => $variation->getSlug(),
        ));

        if ($this->getRequest()->isXmlHttpRequest()) {
            return new JsonResponse(array('variation_url' => $url));
        }

        return $this->redirect($url);
    }

    /**
     * @param ProductInterface $product
     * @param string|null      $currency
     */
    protected function updateSeoMeta(ProductInterface $product, $currency = null)
    {
        $seoPage = $this->get('sonata.seo.page');

        $seoPage->setTitle($product->getName());
        $this->get('sonata.product.seo.facebook')->alterPage($seoPage, $product, $currency);
        $this->get('sonata.product.seo.twitter')->alterPage($seoPage, $product, $currency);
    }
}
