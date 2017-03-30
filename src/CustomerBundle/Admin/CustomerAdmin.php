<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\CustomerBundle\Admin;

use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class CustomerAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setTranslationDomain('SonataCustomerBundle');
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $now = new \DateTime();

        $formMapper
            ->with('customer.group.general', array(
                    'class' => 'col-md-7',
                ))
                ->add('user', 'sonata_type_model_list')
                ->add('firstname')
                ->add('lastname')
                ->add('locale', 'locale')
                ->add('birthDate', 'sonata_type_date_picker', array(
                    'years' => range(1900, $now->format('Y')),
                    'dp_min_date' => '1-1-1900',
                    'dp_max_date' => $now->format('c'),
                ))
                ->add('birthPlace')
            ->end()
            ->with('customer.group.contact', array(
                    'class' => 'col-md-5',
                ))
                ->add('email')
                ->add('phoneNumber')
                ->add('mobileNumber')
                ->add('faxNumber')
                ->add('isFake')
            ->end()
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $list
     */
    public function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('name', 'string', array('code' => '__toString'))
            ->add('user')
            ->add('email')
            ->add('createdAt')
            ->add('locale')
            ->add('isFake')
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $filter
     */
    public function configureShowFields(ShowMapper $filter)
    {
        $filter
            ->with('customer.group.general')
                ->add('user', 'sonata_type_model_list')
                ->add('firstname')
                ->add('lastname')
                ->add('locale', 'locale')
                ->add('birthDate')
                ->add('birthPlace')
            ->end()
            ->with('customer.group.contact')
                ->add('email', 'email')
                ->add('phoneNumber')
                ->add('mobileNumber')
                ->add('faxNumber')
                ->add('isFake')
            ->end()
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $filter
     */
    public function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('firstname')
            ->add('lastname')
            ->add('user')
            ->add('email')
            ->add('locale', null, array(), 'locale')
            ->add('isFake')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, array('edit'))) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        $menu->addChild(
            $this->trans('customer.sidemenu.link_customer_edit', array(), 'SonataCustomerBundle'),
            $admin->generateMenuUrl('edit', array('id' => $id))
        );

        $menu->addChild(
            $this->trans('customer.sidemenu.link_address_list', array(), 'SonataCustomerBundle'),
            $admin->generateMenuUrl('sonata.customer.admin.address.list', array('id' => $id))
        );

        $menu->addChild(
            $this->trans('customer.sidemenu.link_order_list', array(), 'SonataCustomerBundle'),
            $admin->generateMenuUrl('sonata.order.admin.order.list', array('id' => $id))
        );
    }
}
