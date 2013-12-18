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

use Sonata\Component\Product\ProductInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Response;
use Sonata\Component\Basket\BasketElementInterface;
use Sonata\Component\Order\OrderElementInterface;
use Sonata\Component\Basket\BasketInterface;

abstract class BaseProductController extends Controller
{
    /**
     * @param \Sonata\Component\Product\ProductInterface $product
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \RuntimeException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function viewAction($product)
    {
        if (!is_object($product)) {
            throw new NotFoundHttpException('invalid product instance');
        }

        /** @var \Sonata\Component\Product\Pool $productPool */
        $productPool = $this->get('sonata.product.pool');
        $provider = $productPool->getProvider($product);

        $formBuilder = $this->get('form.factory')->createNamedBuilder('add_basket', 'form', null, array('data_class' => $this->container->getParameter('sonata.basket.basket_element.class')));
        $provider->defineAddBasketForm($product, $formBuilder);

        $variationForm = null;
        $rootProduct = $product;

        if ($product->getParent()) {
            $rootProduct = $product->getParent();
        }

        // There is only one combination available so we forward the user
        $enabledVariations = $provider->getEnabledVariations($rootProduct);
        $nbEnabledVariations = count($enabledVariations);

        if (1 == $nbEnabledVariations && $rootProduct == $product) {
            return $this->redirect($this->generateUrl('sonata_product_view', array(
                'productId' => $enabledVariations[0]->getId(),
                'slug' => $enabledVariations[0]->getSlug(),
            )));
        }
        // Product is a parent one but has no activated variation
        elseif (!$nbEnabledVariations && $provider->hasVariations($rootProduct) && $rootProduct != $product) {
            throw new \RuntimeException(sprintf('Product "%s" has no enabled variation', $rootProduct));
        }

        $formType = $productPool->getVariationFormType($rootProduct);
        $formType->setProduct($rootProduct);
        $variationForm = $this->createForm($formType, $product)->createView();

        $form = $formBuilder->getForm()->createView();

        $crossSellingProducts = $this->get('sonata.product.finder')->getCrossSellingSimilarParentProducts($product, 4);
        $currency = $this->get('sonata.price.currency.detector')->getCurrency();

        // Add twitter/FB metadata
        $this->updateSeoMeta($product, $currency);

        return $this->render(
            sprintf('%s:view.html.twig', $provider->getBaseControllerName()),
            array(
                'provider'      => $provider,
                'parent_product' => $rootProduct,
                'product'       => $product,
                'cheapest_variation' => $provider->getCheapestEnabledVariation($product),
                'currency'      => $currency,
                'similar_cross' => $crossSellingProducts,
                'form'          => $form,
                'variation_form' => $variationForm,
            )
        );
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
            'basket'        => $basket
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
            'basket'        => $basket
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
     * @param BasketElementInterface $basketElement
     */
    public function viewBasketElement(BasketElementInterface $basketElement)
    {

    }

    /**
     * @param BasketElementInterface $basketElement
     */
    public function viewBasketElementConfirmation(BasketElementInterface $basketElement)
    {

    }

    /**
     * @param OrderElementInterface $orderElement
     */
    public function viewOrderElement(OrderElementInterface $orderElement)
    {

    }

    /**
     * @param OrderElementInterface $orderElement
     */
    public function viewEditOrderElement(OrderElementInterface $orderElement)
    {

    }

    /**
     * @param ProductInterface $product
     * @param string|null      $currency
     */
    protected function updateSeoMeta(ProductInterface $product, $currency = null)
    {
        $seoPage = $this->get('sonata.seo.page');

        $this->get('sonata.product.seo.facebook')->alterPage($seoPage, $product, $currency);
        $this->get('sonata.product.seo.twitter')->alterPage($seoPage, $product, $currency);
    }
}
