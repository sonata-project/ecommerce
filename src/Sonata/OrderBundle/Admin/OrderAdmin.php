<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\OrderBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;

use Application\Sonata\OrderBundle\Entity\Order;
use Application\Sonata\ProductBundle\Entity\Delivery;
use Application\Sonata\PaymentBundle\Entity\Transaction;

use Knp\Menu\ItemInterface as MenuItemInterface;

class OrderAdmin extends Admin
{
    public function configure()
    {
        $this->parentAssociationMapping = 'customer';
        $this->setTranslationDomain('SonataOrderBundle');
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with($this->trans('order.form.group_main_label'))
                ->add('currency')
                ->add('locale')
                ->add('status', 'sonata_type_translatable_choice', array('choices' => Order::getStatusList(), 'catalogue' => 'SonataOrderBundle'))
                ->add('paymentStatus', 'sonata_type_translatable_choice', array('choices' => Transaction::getStatusList(), 'catalogue' => 'SonataPaymentBundle'))
                ->add('deliveryStatus', 'sonata_type_translatable_choice', array('choices' => Delivery::getStatusList(), 'catalogue' => 'SonataOrderBundle'))
                ->add('validatedAt')
            ->end()
            ->with($this->trans('order.form.group_billing_label'), array('collapsed' => true))
                ->add('billingName')
                ->add('billingAddress1')
                ->add('billingAddress2')
                ->add('billingAddress3')
                ->add('billingCity')
                ->add('billingPostcode')
                ->add('billingCountryCode', 'country')
                ->add('billingFax')
                ->add('billingEmail')
                ->add('billingMobile')
            ->end()
            ->with($this->trans('order.form.group_shipping_label'), array('collapsed' => true))
                ->add('shippingName')
                ->add('shippingAddress1')
                ->add('shippingAddress2')
                ->add('shippingAddress3')
                ->add('shippingCity')
                ->add('shippingPostcode')
                ->add('shippingCountryCode', 'country')
                ->add('shippingFax')
                ->add('shippingEmail')
                ->add('shippingMobile')
            ->end()
        ;

        if (!$this->isChild()) {
            $formMapper
                ->with($this->trans('order.form.group_main_label', array(), 'SonataOrderBundle'))
                    ->add('customer', 'sonata_type_model', array(), array('edit' => 'list'))
                ->end()
            ;
        }
    }

    public function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->addIdentifier('reference')
            ->add('customer')
            ->add('locale')
            ->add('getStatusName', 'trans', array('name' => 'status', 'catalogue' => 'SonataOrderBundle', 'sortable' => 'status'))
            ->add('getDeliveryStatusName', 'trans', array('name' => 'deliveryStatus', 'catalogue' => 'SonataOrderBundle', 'sortable' => 'deliveryStatus'))
            ->add('getPaymentStatusName', 'trans', array('name' => 'paymentStatus', 'catalogue' => 'SonataPaymentBundle', 'sortable' => 'paymentStatus'))
            ->add('validatedAt')
            ->add('totalExcl', 'currency', array('currency' => 'EUR')) // for now the currency is not handled
        ;
    }

    public function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('reference')
            ->add('customer')
            ->add('locale')
        ;
    }

    public function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
    }

    /**
     * @param \Knp\Menu\MenuItemInterface $menu
     * @param $action
     * @param null|\Sonata\AdminBundle\Admin\Admin $childAdmin
     * @return
     */
    protected function configureSideMenu(MenuItemInterface $menu, $action, Admin $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, array('edit'))) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        $menu->addChild(
            $this->trans('order.sidemenu.link_order_edit', array(), 'SonataOrderBundle'),
            array('uri' => $admin->generateUrl('edit', array('id' => $id)))
        );

        $menu->addChild(
            $this->trans('order.sidemenu.link_order_elements_list', array(), 'SonataOrderBundle'),
            array('uri' => $admin->generateUrl('sonata.order.admin.order_element.list', array('id' => $id)))
        );
    }
}