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

use Knplabs\MenuBundle\Menu;
use Knplabs\MenuBundle\MenuItem;

class CustomerAdmin extends BaseCustomerAdmin
{

    protected $class = 'Application\Sonata\CustomerBundle\Entity\Customer';
    protected $baseControllerName = 'SonataCustomerBundle:CustomerAdmin';

    protected $form = array(
        'firstname',
        'lastname'
    );

    protected $list = array(
        'name' => array('code' => '__toString', 'identifier' => true, 'type' => 'string'),
        'createdAt'
    );

    protected $filter = array(
        'name' => array('type' => 'string'),
//        'createdAt'
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
        $translator = $this->container->get('translator');

        $menu = new Menu;

        $admin = $this->isChild() ? $this->getParent() : $this;

        $customerId = $this->container->get('request')->get('id');

        $menu->addChild(
            $translator->trans('link_customer_edit', array(), 'SonataCustomerBundle'),
            $admin->generateUrl('edit', array('id' => $customerId))
        );

        $menu->addChild(
            $translator->trans('link_address_list', array(), 'SonataCustomerBundle'),
            $admin->generateUrl('address.list', array('id' => $customerId))
        );

        $menu->addChild(
            $translator->trans('link_order_list', array(), 'SonataCustomerBundle'),
            $admin->generateUrl('order.list', array('id' => $customerId))
        );

        return $menu;
    }
}