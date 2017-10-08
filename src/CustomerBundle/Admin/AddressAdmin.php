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

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class AddressAdmin extends AbstractAdmin
{
    protected $translationDomain = 'SonataCustomerBundle';

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
        // NEXT_MAJOR: Keep FQCN when bumping Symfony requirement to 2.8+.
        if (method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            $addressTypeType = 'Sonata\CustomerBundle\Form\Type\AddressTypeType';
            $modelListType = 'Sonata\AdminBundle\Form\Type\ModelListType';
            $countryType = 'Symfony\Component\Form\Extension\Core\Type\CountryType';
        } else {
            $addressTypeType = 'sonata_customer_address_types';
            $modelListType = 'sonata_type_model_list';
            $countryType = 'country';
        }

        $formMapper
            ->with($this->trans('address.form.group_contact_label', [], 'SonataCustomerBundle'), [
                'class' => 'col-md-7',
            ])
                ->add('firstname')
                ->add('lastname')
                ->add('phone')
            ->end()
        ;

        $formMapper
            ->with($this->trans('address.form.group_advanced_label', [], 'SonataCustomerBundle'), [
                'class' => 'col-md-5',
            ])
                ->add('type', $addressTypeType, ['translation_domain' => 'SonataCustomerBundle'])
                ->add('current', null, ['required' => false])
                ->add('name')
            ->end();

        if (!$this->isChild()) {
            $formMapper->with($this->trans('address.form.group_contact_label', [], 'SonataCustomerBundle'))
                ->add('customer', $modelListType)
            ->end()
            ;
        }

        $formMapper
            ->with($this->trans('address.form.group_address_label', [], 'SonataCustomerBundle'), [
                'class' => 'col-md-12',
            ])
                ->add('address1')
                ->add('address2')
                ->add('address3')
                ->add('postcode')
                ->add('city')
                ->add('countryCode', $countryType)
            ->end()
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $list
     */
    public function configureListFields(ListMapper $list)
    {
        // NEXT_MAJOR: Keep FQCN when bumping Symfony requirement to 2.8+.
        if (method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            $textType = 'Symfony\Component\Form\Extension\Core\Type\TextType';
        } else {
            $textType = 'text';
        }

        $list
            ->addIdentifier('name')
            ->add('fulladdress', $textType, ['code' => 'getFullAddressHtml', 'template' => 'SonataCustomerBundle:Admin:list_address.html.twig'])
            ->add('current')
            ->add('typeCode', 'trans', ['catalogue' => $this->translationDomain])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        // NEXT_MAJOR: Keep FQCN when bumping Symfony requirement to 2.8+.
        if (method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            $addressTypeType = 'Sonata\CustomerBundle\Form\Type\AddressTypeType';
        } else {
            $addressTypeType = 'sonata_customer_address_types';
        }

        $filter
            ->add('current')
            ->add('type', null, [], $addressTypeType, ['translation_domain' => 'SonataCustomerBundle'])
        ;

        if (!$this->isChild()) {
            $filter
                ->add('customer')
            ;
        }
    }
}
