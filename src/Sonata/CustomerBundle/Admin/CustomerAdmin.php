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
use Knp\Bundle\MenuBundle\MenuItem;

class CustomerAdmin extends BaseCustomerAdmin
{
    protected $form = array(
        'firstname',
        'lastname'
    );

    protected $list = array(
        'name' => array('code' => '__toString', 'identifier' => true, 'type' => 'string'),
        'createdAt'
    );

    protected $filter = array(
        'firstname' => array('type' => 'string'),
        'lastname' => array('type' => 'string'),
//        'createdAt'
    );

    public function configureSideMenu(MenuItem $menu, $action, Admin $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, array('edit'))) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        $menu->addChild(
            $this->trans('sonata.customer.link_customer_edit', array(), 'SonataCustomerBundle'),
            $admin->generateUrl('edit', array('id' => $id))
        );

        $menu->addChild(
            $this->trans('sonata.customer.link_address_list', array(), 'SonataCustomerBundle'),
            $admin->generateUrl('sonata.customer.admin.address.list', array('id' => $id))
        );

        $menu->addChild(
            $this->trans('sonata.customer.link_order_list', array(), 'SonataCustomerBundle'),
            $admin->generateUrl('sonata.customer.admin.order.list', array('id' => $id))
        );
    }
}