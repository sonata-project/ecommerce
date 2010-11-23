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


    public function indexAction() {

    }

    public function viewAction($product_id, $slug) {

        $em             = $this->get('doctrine.orm.default_entity_manager');
        $repository     = $em->getRepository('ProductBundle:Product');

        $product = is_object($product_id) ? $product_id : $repository->findOneById($product_id);

        if(!$product) {
            throw new NotFoundHttpException(sprintf('Unable to find the product with id=%d', $product_id));
        }

        // generate the session
        $this->get('session')->start();

        $code = $this->get('sonata.product.pool')->getProductCode($product);
        
        return $this->forward(sprintf('ProductBundle:%s:view', ucfirst($code)), array(
            'product' => $product
        ));
    }

    public function viewVariationsAction($product_id, $slug) {

    }
}