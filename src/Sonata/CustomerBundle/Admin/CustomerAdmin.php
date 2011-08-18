<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\CustomerBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Knp\Bundle\MenuBundle\MenuItem;

class CustomerAdmin extends Admin
{
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('firstname')
            ->add('lastname')
        ;
    }

    public function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('name', 'string', array('code' => '__toString'))
            ->add('createdAt')
        ;
    }

    public function configureShowFields(ShowMapper $filter)
    {
        $filter
            ->add('firstname')
            ->add('lastname')
        ;
    }


    public function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('firstname')
            ->add('lastname')
        ;
    }

    public function configureSideMenu(MenuItem $menu, $action, Admin $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, array('edit'))) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        $menu->addChild(
            $this->trans('link_customer_edit', array(), 'SonataCustomerBundle'),
            $admin->generateUrl('edit', array('id' => $id))
        );

        $menu->addChild(
            $this->trans('link_address_list', array(), 'SonataCustomerBundle'),
            $admin->generateUrl('sonata.customer.admin.address.list', array('id' => $id))
        );

        $menu->addChild(
            $this->trans('link_order_list', array(), 'SonataCustomerBundle'),
            $admin->generateUrl('sonata.customer.admin.order.list', array('id' => $id))
        );
    }
}