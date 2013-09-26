<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\InvoiceBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class InvoiceAdmin extends Admin
{
    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setTranslationDomain('SonataInvoiceBundle');
    }

    /**
     * {@inheritDoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        if (!$this->isChild()) {
            $formMapper
                ->with($this->trans('invoice.form.group_main_label', array(), 'SonataInvoiceBundle'))
                    ->add('customer', 'sonata_type_model_list')
                ->end()
            ;
        }

        $formMapper
            ->with($this->trans('invoice.form.group_main_label', array(), 'SonataInvoiceBundle'))
                ->add('reference')
                ->add('currency', 'sonata_currency')
                ->add('status')
                ->add('totalInc')
                ->add('totalExcl')
            ->end()
            ->with($this->trans('invoice.form.group_billing_label', array(), 'SonataInvoiceBundle'), array('collapsed' => true))
                ->add('name')
                ->add('phone')
                ->add('address1')
                ->add('address2')
                ->add('address3')
                ->add('city')
                ->add('postcode')
                ->add('country', 'country')
                ->add('fax')
                ->add('email')
                ->add('mobile')
            ->end()
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->addIdentifier('reference')
            ->add('customer')
            ->add('status')
            ->add('totalExcl')
        ;
    }
}
