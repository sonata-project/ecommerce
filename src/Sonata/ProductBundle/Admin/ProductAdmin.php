<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Knp\Bundle\MenuBundle\MenuItem;

class ProductAdmin extends BaseProductAdmin
{
    protected $list = array(
        'enabled',
        'name' => array('identifier' => true),
        'price',
        'stock',
    );

    protected $form = array(
        'name',
        'sku' => array('type' => 'string'),
        'description',
        'price',
        'vat',
        'stock',
        'image' => array('edit' => 'list')
    );

    protected $filter = array(
        'name',
//        'price',
        'enabled'
    );

    public function configureSideMenu(MenuItem $menu, $action, Admin $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, array('edit'))) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        $menu->addChild(
            $this->trans('sonata.product.link_product_edit', array(), 'SonataProductBundle'),
            $admin->generateUrl('edit', array('id' => $id))
        );

        $menu->addChild(
            $this->trans('sonata.product.link_variation_list', array(), 'SonataProductBundle'),
            $admin->generateUrl('sonata.product.admin.variation.list', array('id' => $id))
        );

        $menu->addChild(
            $this->trans('sonata.product.link_category_list', array(), 'SonataProductBundle'),
            $admin->generateUrl('sonata.product.admin.category.list', array('id' => $id))
        );

        $menu->addChild(
            $this->trans('sonata.product.link_delivery_list', array(), 'SonataProductBundle'),
            $admin->generateUrl('sonata.product.admin.delivery.list', array('id' => $id))
        );
    }
}
