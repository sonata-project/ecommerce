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

namespace Sonata\ProductBundle\Controller;

use Sonata\Component\Basket\BasketElementInterface;
use Sonata\Component\Basket\BasketInterface;
use Sonata\Component\Form\Type\VariationChoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PropertyAccess\PropertyAccess;

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
        $product = $this->get('sonata.product.set.manager')->findEnabledFromIdAndSlug($productId, $slug);

        if (!$product) {
            throw new NotFoundHttpException(sprintf('Unable to find the product with id=%d', $productId));
        }

        /** @var \Sonata\Component\Product\Pool $productPool */
        $productPool = $this->get('sonata.product.pool');
        $provider = $productPool->getProvider($product);

        if ($provider->hasVariations($product)) {
            if (!$provider->hasEnabledVariations($product)) {
                throw new NotFoundHttpException('Product has no activated variation');
            }
            // We display the cheapest variation
            $product = $provider->getCheapestEnabledVariation($product);
        }

        $action = sprintf('%s:viewAction', $provider->getBaseControllerName());
        $response = $this->forward($action, [
            'provider' => $provider,
            'product' => $product,
        ]);

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
     * @param FormView               $formView
     * @param BasketElementInterface $basketElement
     * @param BasketInterface        $basket
     *
     * @return Response
     */
    public function renderFormBasketElementAction(FormView $formView, BasketElementInterface $basketElement, BasketInterface $basket)
    {
        $action = sprintf('%s:renderFormBasketElementAction', $basketElement->getProductProvider()->getBaseControllerName());

        $response = $this->forward($action, [
            'formView' => $formView,
            'basketElement' => $basketElement,
            'basket' => $basket,
        ]);

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
     * Returns price for $productId in given quantity and stock information
     * as JSON.
     *
     * @param $productId
     *
     * @return JsonResponse
     */
    public function getPriceStockForQuantityAction(Request $request, $productId)
    {
        if (!$request->isXmlHttpRequest() || !($quantity = (int) $request->query->get('quantity'))) {
            throw new BadRequestHttpException('Request needs to be XHR and have a quantity parameter');
        }

        $product = $this->get('sonata.product.set.manager')->findOneBy(['id' => $productId, 'enabled' => true]);

        if (!$product) {
            throw new NotFoundHttpException(sprintf('Unable to find the product with id=%d', $productId));
        }

        $errors = [];

        /** @var \Sonata\Component\Product\Pool $productPool */
        $productPool = $this->get('sonata.product.pool');
        $provider = $productPool->getProvider($product);

        if ($provider->hasVariations($product)) {
            $errors['variations'] = 'This is a master product, it has no price';
        }

        $stock = $provider->getStockAvailable($product) ?: 0;

        if ($quantity > $stock) {
            $errors['stock'] = $this->get('translator')->trans('product_out_of_stock_quantity', [], 'SonataProductBundle');
        }

        $currency = $this->get('sonata.basket')->getCurrency();

        $price = $provider->calculatePrice($product, $currency, true, $quantity);

        return new JsonResponse([
            'stock' => $stock,
            'price' => $price,
            'price_text' => $this->get('sonata.intl.templating.helper.number')->formatCurrency($price, $currency),
            'errors' => $errors,
        ]);
    }

    /**
     * @param BasketElementInterface $basketElement
     * @param BasketInterface        $basket
     *
     * @return Response
     */
    public function renderFinalReviewBasketElementAction(BasketElementInterface $basketElement, BasketInterface $basket)
    {
        $action = sprintf('%s:renderFinalReviewBasketElementAction', $basketElement->getProductProvider()->getBaseControllerName());

        $response = $this->forward($action, [
            'basketElement' => $basketElement,
            'basket' => $basket,
        ]);

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
        $product = \is_object($productId) ? $productId : $this->get('sonata.product.set.manager')->findEnabledFromIdAndSlug($productId, $slug);

        if (!$product) {
            throw new NotFoundHttpException(sprintf('Unable to find the product with id=%d', $productId));
        }

        $provider = $this->get('sonata.product.pool')->getProvider($product);

        $action = sprintf('%s:viewVariationsAction', $provider->getBaseControllerName());
        $response = $this->forward($action, [
                'product' => $product,
            ]);

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
     * @param $productId
     * @param $slug
     *
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function variationToProductAction(Request $request, $productId, $slug)
    {
        $product = \is_object($productId) ? $productId : $this->get('sonata.product.set.manager')->findEnabledFromIdAndSlug($productId, $slug);

        if (!$product) {
            throw new NotFoundHttpException(sprintf('Unable to find the product with id=%d', $productId));
        }

        if (null !== $product->getParent()) {
            // We need the master product
            $product = $product->getParent();
        }

        $provider = $this->get('sonata.product.pool')->getProvider($product);

        $data = $request->query->get('sonata_product_variation_choices');

        $choices = $provider->getVariationsChoices($product, array_keys($data));

        $accessor = PropertyAccess::createPropertyAccessor();
        $currentValues = [];

        foreach ($choices as $field => $values) {
            $currentValues[$field] = array_search($accessor->getValue($product, $field), $values, true);
        }

        $form = $this->createForm(VariationChoiceType::class, $currentValues, [
            'product' => $product,
            'fields' => array_keys($data),
        ]);

        $form->handleRequest($request);

        $selectedVariation = $form->getData();

        // Retrieving correct values
        foreach ($selectedVariation as $key => $value) {
            $selectedVariation[$key] = $choices[$key][$value];
        }

        $variation = $provider->getVariation($product, $selectedVariation);

        $action = sprintf('%s:variationToProductAction', $provider->getBaseControllerName());
        $response = $this->forward($action, [
            'product' => $product,
            'variation' => $variation,
        ]);

        if ($this->get('kernel')->isDebug() && !($response instanceof JsonResponse)) {
            $response->setContent(sprintf("\n<!-- [Sonata] Product code: %s, id: %s, action: %s  -->\n%s\n<!-- [Sonata] end product -->\n",
                $this->get('sonata.product.pool')->getProductCode($product),
                $product->getId(),
                $action,
                $response->getContent()
            ));
        }

        return $response;
    }
}
