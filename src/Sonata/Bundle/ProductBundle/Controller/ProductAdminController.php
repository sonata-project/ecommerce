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

use Bundle\BaseApplicationBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class ProductAdminController extends Controller
{
    protected $class = 'Application\ProductBundle\Entity\Product';

    protected $list_fields = array(
        'id',
        'enabled',
        'name',
        'price',
        'stock',
    );

    protected $base_route = 'sonata_admin_product';

    public function editAction($product_id) {

    }

    public function updateAction() {

    }

    public function deliveryIndexAction($product_id) {

    }

    public function deliveryEditAction($product_id, $delivery_id) {

    }

    public function deliveryUpdateAction($product_id) {

    }
}