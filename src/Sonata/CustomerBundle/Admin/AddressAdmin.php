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
use Application\Sonata\CustomerBundle\Entity\Address;

class AddressAdmin extends Admin
{
    public function configure()
    {
        $this->parentAssociationMapping = 'customer';
        $this->setTranslationDomain('SonataCustomerBundle');
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with($this->trans('address.form.group_advanced_label', array(), 'SonataCustomerBundle'))
                ->add('type', 'choice', array('choices' => Address::getTypesList()))
                ->add('current')
                ->add('name')
            ->end();

        $formMapper
            ->with($this->trans('address.form.group_contact_label', array(), 'SonataCustomerBundle'))
                ->add('firstname')
                ->add('lastname')
                ->add('phone')
            ->end()
        ;

        if (!$this->isChild()) {
            $formMapper->with($this->trans('address.form.group_contact_label', array(), 'SonataCustomerBundle'))
                ->add('customer', 'sonata_type_model', array(),  array('edit' => 'list'))
            ->end()
            ;
        }

        $formMapper
            ->with($this->trans('address.form.group_address_label', array(), 'SonataCustomerBundle'))
                ->add('address1')
                ->add('address2')
                ->add('address3')
                ->add('postcode')
                ->add('city')
                ->add('countryCode', 'country')
            ->end()
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $list
     */
    public function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('fulladdress', 'string', array('code' => 'getFullAddress'))
            ->addIdentifier('name')
            ->add('current')
            ->add('typeCode', 'string')
        ;
    }
}