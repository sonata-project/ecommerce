<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Sonata\Component\Payment\Transaction;

class ProductController extends Controller
{

    public function viewAction($product_id, $slug)
    {

        $em             = $this->get('doctrine.orm.default_entity_manager');
        $repository     = $em->getRepository('ProductBundle:Product');

        $product = is_object($product_id) ? $product_id : $repository->findOneById($product_id);

        if(!$product) {
            throw new NotFoundHttpException(sprintf('Unable to find the product with id=%d', $product_id));
        }

        // generate the session
        $this->get('session')->start();

        $code = $this->get('sonata.product.pool')->getProductCode($product);

        $action = sprintf('ProductBundle:%s:view', ucfirst($code));
        $response = $this->forward($action, array(
            'product' => $product
        ));

        if($this->get('kernel')->isDebug()) {
            $response->setContent(sprintf("\n<!-- [Sonata] Product code: %s, id: %s, action: %s  -->\n%s\n<!-- [Sonata] end product -->\n",
                $code,
                $product->getId(),
                $action,
                $response->getContent()
            ));
        }

        return $response;
    }

    public function renderFormBasketElementAction($field_group, $basket_element)
    {

        $code   = $this->get('sonata.product.pool')->getProductCode($basket_element->getProduct());
        $action = sprintf('ProductBundle:%s:renderFormBasketElement', ucfirst($code)) ;

        $response = $this->forward($action, array(
            'field_group'     => $field_group,
            'basket_element'  => $basket_element,
        ));

        if($this->get('kernel')->isDebug()) {
            $response->setContent(sprintf("\n<!-- [Sonata] Product code: %s, id: %s, action: %s -->\n%s\n<!-- [Sonata] end product -->\n",
                $code,
                $basket_element->getProductId(),
                $action,
                $response->getContent()
            ));
        }

        return $response;
    }
    
    public function viewVariationsAction($product_id, $slug) {

    }
}