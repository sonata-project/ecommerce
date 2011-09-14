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
use Sonata\Component\Basket\BasketElement;
use Symfony\Component\Form\FormView;

class ProductController extends Controller
{
    /**
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @param $productId
     * @param $slug
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
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

    /**
     * @param \Symfony\Component\Form\FormView $formView
     * @param \Sonata\Component\Basket\BasketElement $basketElement
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function renderFormBasketElementAction(FormView $formView, BasketElement $basketElement)
    {
        $action = sprintf('%s:renderFormBasketElement', $basketElement->getProductProvider()->getBaseControllerName()) ;

        $response = $this->forward($action, array(
            'formView'       => $formView,
            'basketElement'  => $basketElement,
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
     * @param \Sonata\Component\Basket\BasketElement $basketElement
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function renderFinalReviewBasketElementAction(BasketElement $basketElement)
    {
        $action = sprintf('%s:renderFinalReviewBasketElement',  $basketElement->getProductProvider()->getBaseControllerName()) ;

        $response = $this->forward($action, array(
            'basketElement'  => $basketElement,
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
     * @return void
     */
    public function viewVariationsAction($productId, $slug)
    {

    }
}