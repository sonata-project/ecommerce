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
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class AddressAdmin extends Admin
{
    protected $translationDomain = "SonataCustomerBundle";

    /**
     * {@inheritdoc}
     */
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
            ->with($this->trans('address.form.group_contact_label', array(), 'SonataCustomerBundle'), array(
                'class' => 'col-md-7'
            ))
                ->add('firstname')
                ->add('lastname')
                ->add('phone')
            ->end()
        ;

        $formMapper
            ->with($this->trans('address.form.group_advanced_label', array(), 'SonataCustomerBundle'), array(
                'class' => 'col-md-5'
            ))
                ->add('type', 'sonata_customer_address_types', array('translation_domain' => 'SonataCustomerBundle'))
                ->add('current', null, array('required' => false))
                ->add('name')
            ->end();

        if (!$this->isChild()) {
            $formMapper->with($this->trans('address.form.group_contact_label', array(), 'SonataCustomerBundle'))
                ->add('customer', 'sonata_type_model_list')
            ->end()
            ;
        }

        $formMapper
            ->with($this->trans('address.form.group_address_label', array(), 'SonataCustomerBundle'), array(
                'class' => 'col-md-12'
            ))
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
            ->addIdentifier('name')
            ->add('fulladdress', 'string', array('code' => 'getFullAddressHtml', 'template' => "SonataCustomerBundle:Admin:list_address.html.twig"))
            ->add('current')
            ->add('typeCode', 'trans', array('catalogue' => $this->translationDomain))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('current')
            ->add('type', null, array(), 'sonata_customer_address_types', array('translation_domain' => 'SonataCustomerBundle'))
        ;

        if (!$this->isChild()) {
            $filter
                ->add('customer')
            ;
        }
    }


}
