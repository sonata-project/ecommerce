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

use Knplabs\MenuBundle\Menu;
use Knplabs\MenuBundle\MenuItem;

class ProductAdmin extends BaseProductAdmin
{

    protected $class = 'Application\Sonata\ProductBundle\Entity\Product';
    protected $baseControllerName = 'SonataProductBundle:ProductAdmin';

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


    public function getSideMenu($action, $childAdmin = false)
    {

        if ($childAdmin || in_array($action, array('edit'))) {
            return $this->getEditSideMenu();
        }

        return false;
    }

    public function getEditSideMenu()
    {

        $menu = new Menu;

        $admin = $this->isChild() ? $this->getParent() : $this;

        $productId = $this->container->get('request')->get('id');

        $menu->addChild(
            $this->trans('link_edit_product', array(), 'SonataProductBundle'),
            $admin->generateUrl('edit', array('id' => $productId))
        );

        $menu->addChild(
            $this->trans('link_variation_list', array(), 'SonataProductBundle'),
            $admin->generateUrl('variation_list', array('id' => $productId))
        );

        $menu->addChild(
            $this->trans('link_variation_list', array(), 'SonataProductBundle'),
            $admin->generateUrl('variation_list', array('id' => $productId))
        );

        $menu->addChild(
            $this->trans('link_category_list', array(), 'SonataProductBundle'),
            $admin->generateUrl('category', array('id' => $productId))
        );

        $menu->addChild(
            $this->trans('link_delivery_list', array(), 'SonataProductBundle'),
            $admin->generateUrl('product_delivery.list', array('id' => $productId))
        );

        return $menu;
    }
}