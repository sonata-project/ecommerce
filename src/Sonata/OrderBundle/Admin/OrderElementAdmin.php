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

use Application\Sonata\OrderBundle\Entity\OrderElement;

class OrderElementAdmin extends Admin
{
    public function configure()
    {
        $this->parentAssociationMapping = 'order';
        $this->setTranslationDomain('SonataOrderBundle');
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with($this->trans('order_element.form.group_main_label', array(), 'SonataOrderBundle'))
                ->add('productType')
                ->add('quantity')
                ->add('price')
                ->add('vat')
                ->add('designation')
                ->add('description')
                ->add('status', 'sonata_type_translatable_choice', array('catalogue' => 'SonataOrderBundle', 'choices' => OrderElement::getStatusList()))
                ->add('deliveryStatus', 'sonata_type_translatable_choice', array('catalogue' => 'SonataOrderBundle', 'choices' => OrderElement::getDeliveryStatusList()))
            ->end()
        ;
    }

    public function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->add('order')
            ->add('productType')
            ->add('getStatusName', 'trans', array('name' => 'status', 'catalogue' => 'SonataOrderBundle', 'stortable' => 'status'))
            ->add('getDeliveryStatusName', 'trans', array('name' => 'deliveryStatus', 'catalogue' => 'SonataOrderBundle', 'stortable' => 'deliveryStatus'))
            ->add('getTotalWithVat', 'currency', array('currency' => 'EUR')) // for now the currency is not handled
            ->add('getTotal', 'currency', array('currency' => 'EUR')) // for now the currency is not handled
        ;
    }
}