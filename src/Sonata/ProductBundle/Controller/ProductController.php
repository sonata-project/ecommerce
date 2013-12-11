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
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Response;
use Sonata\Component\Basket\BasketElementInterface;
use Sonata\Component\Basket\BasketInterface;

class ProductController extends Controller
{
    /**
     * @param $productId
     * @param $slug
     *
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function viewAction($productId, $slug)
    {
        $product = is_object($productId) ? $productId : $this->get('sonata.product.set.manager')->findEnabledFromIdAndSlug($productId, $slug);

        if (!$product) {
            throw new NotFoundHttpException(sprintf('Unable to find the product with id=%d', $productId));
        }

        /** @var \Sonata\Component\Product\Pool $productPool */
        $productPool = $this->get('sonata.product.pool');
        $provider = $productPool->getProvider($product);

        if ($provider->hasVariations($product) && !$provider->hasEnabledVariations($product)) {
            throw new NotFoundHttpException('Product has no activated variation');
        }

        $action = sprintf('%s:view', $provider->getBaseControllerName());
        $response = $this->forward($action, array(
            'provider' => $provider,
            'product' => $product
        ));

        if ($this->get('kernel')->isDebug()) {
            $response->setContent(sprintf("\n<!-- [Sonata] Product code: %s, id: %s, action: %s  -->\n%s\n<!-- [Sonata] end product -->\n",
                    $this->get('sonata.product.pool')->getProductCode($product),
                    $product->getId(),
                    $action,
                    $response->getContent()
                ));
        }

        return $response;
    }

    /**
     * @param \Symfony\Component\Form\FormView                $formView
     * @param \Sonata\Component\Basket\BasketElementInterface $basketElement
     * @param \Sonata\Component\Basket\BasketInterface        $basket
     *
     * @return Response
     */
    public function renderFormBasketElementAction(FormView $formView, BasketElementInterface $basketElement, BasketInterface $basket)
    {
        $action = sprintf('%s:renderFormBasketElement', $basketElement->getProductProvider()->getBaseControllerName()) ;

        $response = $this->forward($action, array(
                'formView'       => $formView,
                'basketElement'  => $basketElement,
                'basket'         => $basket
            ));

        if ($this->get('kernel')->isDebug()) {
            $response->setContent(sprintf("\n<!-- [Sonata] Product code: %s, id: %s, action: %s -->\n%s\n<!-- [Sonata] end product -->\n",
                    $basketElement->getProductCode(),
                    $basketElement->getProductId(),
                    $action,
                    $response->getContent()
                ));
        }

        return $response;
    }

    /**
     * @param \Sonata\Component\Basket\BasketElementInterface $basketElement
     * @param \Sonata\Component\Basket\BasketInterface        $basket
     *
     * @return Response
     */
    public function renderFinalReviewBasketElementAction(BasketElementInterface $basketElement, BasketInterface $basket)
    {
        $action = sprintf('%s:renderFinalReviewBasketElement',  $basketElement->getProductProvider()->getBaseControllerName()) ;

        $response = $this->forward($action, array(
                'basketElement'  => $basketElement,
                'basket'         => $basket
            ));

        if ($this->get('kernel')->isDebug()) {
            $response->setContent(sprintf("\n<!-- [Sonata] Product code: %s, id: %s, action: %s -->\n%s\n<!-- [Sonata] end product -->\n",
                    $basketElement->getProductCode(),
                    $basketElement->getProductId(),
                    $action,
                    $response->getContent()
                ));
        }

        return $response;
    }

    /**
     * @param $productId
     * @param $slug
     *
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function viewVariationsAction($productId, $slug)
    {
        $product = is_object($productId) ? $productId : $this->get('sonata.product.set.manager')->findEnabledFromIdAndSlug($productId, $slug);

        if (!$product) {
            throw new NotFoundHttpException(sprintf('Unable to find the product with id=%d', $productId));
        }

        $provider = $this->get('sonata.product.pool')->getProvider($product);

        $action = sprintf('%s:viewVariations', $provider->getBaseControllerName());
        $response = $this->forward($action, array(
                'product' => $product
            ));

        if ($this->get('kernel')->isDebug()) {
            $response->setContent(sprintf("\n<!-- [Sonata] Product code: %s, id: %s, action: %s  -->\n%s\n<!-- [Sonata] end product -->\n",
                    $this->get('sonata.product.pool')->getProductCode($product),
                    $product->getId(),
                    $action,
                    $response->getContent()
                ));
        }

        return $response;
    }

    /**
     * Displays breadcrumb for a product
     *
     * @param int $productId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function displayBreadcrumbAction($productId)
    {
        $product = $this->get('sonata.product.set.manager')->findOneBy(
            array('id' => $productId, 'enabled' => true)
        );

        if (!$product) {
            throw new NotFoundHttpException(sprintf('Unable to find the product with id=%d', $productId));
        }

        $categories = $this->get('sonata.classification.manager.category')->getCategories();

        $selectedCategory = $product->getMainCategory();

        if (!$selectedCategory) {
            throw new NotFoundHttpException(sprintf('Product "%d" has no main category', $productId));
        }

        $categoryId = $selectedCategory->getId();

        $sorted = array(
            $categories[$categoryId]
        );

        while ($category = $categories[$categoryId]->getParent()) {
            $sorted[] = $category;
            $categoryId = $category->getId();
        }

        $sorted = array_reverse($sorted, true);

        return $this->render('SonataProductBundle:Product:display_breadcrumb.html.twig', array(
            'categories' => $sorted,
            'product'    => $product,
        ));
    }
}
