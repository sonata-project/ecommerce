<?php

declare(strict_types=1);

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
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\CustomerBundle\Form\Type\AddressTypeType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AddressAdmin extends AbstractAdmin
{
    protected $translationDomain = 'SonataCustomerBundle';

    public function configure(): void
    {
        $this->parentAssociationMapping = 'customer';
        $this->setTranslationDomain('SonataCustomerBundle');
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     */
    public function configureFormFields(FormMapper $formMapper): void
    {
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
                ->add('type', AddressTypeType::class, ['translation_domain' => 'SonataCustomerBundle'])
                ->add('current', null, ['required' => false])
                ->add('name')
            ->end();

        if (!$this->isChild()) {
            $formMapper->with($this->trans('address.form.group_contact_label', [], 'SonataCustomerBundle'))
                ->add('customer', ModelListType::class)
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
                ->add('countryCode', CountryType::class)
            ->end()
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $list
     */
    public function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('name')
            ->add('fulladdress', TextType::class, [
                'code' => 'getFullAddressHtml',
                'template' => 'SonataCustomerBundle:Admin:list_address.html.twig',
            ])
            ->add('current')
            ->add('typeCode', 'trans', ['catalogue' => $this->translationDomain])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('current')
            ->add('type', null, [], AddressTypeType::class, ['translation_domain' => 'SonataCustomerBundle'])
        ;

        if (!$this->isChild()) {
            $filter
                ->add('customer')
            ;
        }
    }
}
