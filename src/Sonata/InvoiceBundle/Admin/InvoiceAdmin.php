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
use Sonata\Component\Currency\CurrencyDetectorInterface;

class InvoiceAdmin extends Admin
{
    /**
     * @var CurrencyDetectorInterface
     */
    protected $currencyDetector;

    /**
     * @param CurrencyDetectorInterface $currencyDetector
     */
    public function setCurrencyDetector(CurrencyDetectorInterface $currencyDetector)
    {
        $this->currencyDetector = $currencyDetector;
    }

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setTranslationDomain('SonataInvoiceBundle');
    }

    /**
     * {@inheritdoc}
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
                ->add('status', 'sonata_invoice_status', array('translation_domain' => 'SonataInvoiceBundle'))
                ->add('totalExcl')
                ->add('totalInc')
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
     * {@inheritdoc}
     */
    public function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->addIdentifier('reference')
            ->add('customer')
            ->add('status', 'string', array('template' => 'SonataInvoiceBundle:InvoiceAdmin:list_status.html.twig'))
            ->add('totalExcl', 'currency', array('currency' => $this->currencyDetector->getCurrency()->getLabel()))
            ->add('totalInc', 'currency', array('currency' => $this->currencyDetector->getCurrency()->getLabel()))
        ;
    }
}
