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

use Application\Sonata\PaymentBundle\Entity\Transaction;

class ProductController extends Controller
{

    public function viewAction($productId, $slug)
    {

        $product = is_object($productId) ? $productId : $this->get('sonata.product.collection.manager')->findOneBy(array('id' =>  $productId));

        if (!$product) {
            throw new NotFoundHttpException(sprintf('Unable to find the product with id=%d', $productId));
        }

        $provider = $this->get('sonata.product.pool')->getProvider($product);

        $action = sprintf('%s:view', $provider->getBaseControllerName());
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

    public function renderFormBasketElementAction($fieldGroup, $basketElement)
    {

        $code   = $this->get('sonata.product.pool')->getProductCode($basketElement->getProduct());
        $action = sprintf('SonataProductBundle:%s:renderFormBasketElement', ucfirst($code)) ;

        $response = $this->forward($action, array(
            'fieldGroup'     => $fieldGroup,
            'basketElement'  => $basketElement,
        ));

        if ($this->get('kernel')->isDebug()) {
            $response->setContent(sprintf("\n<!-- [Sonata] Product code: %s, id: %s, action: %s -->\n%s\n<!-- [Sonata] end product -->\n",
                $code,
                $basketElement->getProductId(),
                $action,
                $response->getContent()
            ));
        }

        return $response;
    }

    public function renderFinalReviewBasketElementAction($basketElement)
    {
        $code   = $this->get('sonata.product.pool')->getProductCode($basketElement->getProduct());
        $action = sprintf('SonataProductBundle:%s:renderFinalReviewBasketElement', ucfirst($code)) ;

        $response = $this->forward($action, array(
            'basketElement'  => $basketElement,
        ));

        if ($this->get('kernel')->isDebug()) {
            $response->setContent(sprintf("\n<!-- [Sonata] Product code: %s, id: %s, action: %s -->\n%s\n<!-- [Sonata] end product -->\n",
                $code,
                $basketElement->getProductId(),
                $action,
                $response->getContent()
            ));
        }

        return $response;
    }

    public function viewVariationsAction($productId, $slug) {

    }
}