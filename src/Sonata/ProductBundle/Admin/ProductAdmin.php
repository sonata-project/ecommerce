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
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;

class ProductAdmin extends Admin
{
    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     * @return void
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('sku')
            ->add('description')
            ->add('price')
            ->add('vat')
            ->add('stock')
            ->add('image', 'sonata_type_model', array(), array('edit' => 'list'))
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $list
     * @return void
     */
    public function configureListFields(ListMapper $list)
    {
        $list
            ->add('enabled')
            ->addIdentifier('name')
            ->add('price')
            ->add('stock')
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $filter
     * @return void
     */
    public function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('name')
//            ->add('price')
            ->add('enabled')
        ;
    }

    /**
     * @param \Knp\Menu\ItemInterface $menu
     * @param $action
     * @param null|\Sonata\AdminBundle\Admin\Admin $childAdmin
     * @return
     */
    public function configureSideMenu(MenuItemInterface $menu, $action, Admin $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, array('edit'))) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        $menu->addChild(
            $this->trans('sidemenu.link_product_edit'),
            array('uri' => $admin->generateUrl('edit', array('id' => $id)))
        );

        $menu->addChild(
            $this->trans('sidemenu.link_category_list'),
            array('uri' => $admin->generateUrl('sonata.product.admin.category.list', array('id' => $id)))
        );

//        $menu->addChild(
//            $this->trans('link_variation_list', array(), 'SonataProductBundle'),
//            array('uri' => $admin->generateUrl('sonata.product.admin.variation.list', array('id' => $id)))
//        );

        $menu->addChild(
            $this->trans('sidemenu.link_delivery_list'),
            array('uri' => $admin->generateUrl('sonata.product.admin.delivery.list', array('id' => $id)))
        );
    }
}
