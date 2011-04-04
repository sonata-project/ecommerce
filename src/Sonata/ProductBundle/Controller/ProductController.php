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

        $em             = $this->get('doctrine.orm.default_entity_manager');
        $repository     = $em->getRepository('Application\Sonata\ProductBundle\Entity\Product');

        $product = is_object($productId) ? $productId : $repository->findOneById($productId);

        if (!$product) {
            throw new NotFoundHttpException(sprintf('Unable to find the product with id=%d', $productId));
        }

        // generate the session
        $this->get('session')->start();

        $code = $this->get('sonata.product.pool')->getProductCode($product);

        if (!$code) {
            throw new NotFoundHttpException(sprintf('Unable to find the product code with product.id=%d', $productId));
        }

        $action = sprintf('SonataProductBundle:%s:view', ucfirst($code));
        $response = $this->forward($action, array(
            'product' => $product
        ));

        if ($this->get('kernel')->isDebug()) {
            $response->setContent(sprintf("\n<!-- [Sonata] Product code: %s, id: %s, action: %s  -->\n%s\n<!-- [Sonata] end product -->\n",
                $code,
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