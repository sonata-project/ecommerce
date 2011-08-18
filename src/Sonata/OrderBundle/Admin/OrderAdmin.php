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

class OrderAdmin extends Admin
{
    protected $parentAssociationMapping = 'customer';

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with($this->trans('form_group_main_label', array(), 'SonataOrderBundle'))
                ->add('currency')
                ->add('status')
                ->add('paymentStatus')
                ->add('deliveryStatus')
                ->add('validatedAt')
            ->end()
            ->with($this->trans('form_group_billing_label', array(), 'SonataOrderBundle'))
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
            ->with($this->trans('form_group_shipping_label', array(), 'SonataOrderBundle'))
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
            ->with($this->trans('form_group_misc_label', array(), 'SonataOrderBundle'))
                ->add('orderElements', 'sonata_type_model', array(), array('edit' => 'inline', 'inline' => 'table'))
            ->end()
        ;
        
        if (!$this->isChild()) {
            $formMapper
                ->with($this->trans('form_group_misc_label', array(), 'SonataOrderBundle'))
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
            ->add('status')
            ->add('paymentStatus')
            ->add('validatedAt')
            ->add('totalExcl')
        ;
    }

    public function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('reference')
//            ->add('customer')
        ;
    }
}