<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\InvoiceBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Component\Currency\CurrencyDetectorInterface;

class InvoiceAdmin extends AbstractAdmin
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
        // NEXT_MAJOR: Keep FQCN when bumping Symfony requirement to 2.8+.
        if (method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            $modelListType = 'Sonata\AdminBundle\Form\Type\ModelListType';
            $currencyType = 'Sonata\Component\Currency\CurrencyFormType';
            $invoiceStatusType = 'Sonata\InvoiceBundle\Form\Type\InvoiceStatusType';
            $countryType = 'Symfony\Component\Form\Extension\Core\Type\CountryType';
        } else {
            $modelListType = 'sonata_type_model_list';
            $currencyType = 'sonata_currency';
            $invoiceStatusType = 'sonata_invoice_status';
            $countryType = 'country';
        }

        if (!$this->isChild()) {
            $formMapper
                ->with($this->trans('invoice.form.group_main_label', [], $this->translationDomain))
                    ->add('customer', $modelListType)
                ->end()
            ;
        }

        $formMapper
            ->with($this->trans('invoice.form.group_main_label', [], $this->translationDomain))
                ->add('reference')
                ->add('currency', $currencyType)
                ->add('status', $invoiceStatusType, ['translation_domain' => $this->translationDomain])
                ->add('totalExcl')
                ->add('totalInc')
            ->end()
            ->with($this->trans('invoice.form.group_billing_label', [], $this->translationDomain), ['collapsed' => true])
                ->add('name')
                ->add('phone')
                ->add('address1')
                ->add('address2')
                ->add('address3')
                ->add('city')
                ->add('postcode')
                ->add('country', $countryType)
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
        // NEXT_MAJOR: Keep FQCN when bumping Symfony requirement to 2.8+.
        if (method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            $textType = 'Symfony\Component\Form\Extension\Core\Type\TextType';
            $currencyType = 'Sonata\Component\Currency\CurrencyFormType';
        } else {
            $textType = 'text';
            $currencyType = 'sonata_currency';
        }

        $list
            ->addIdentifier('reference')
            ->add('customer')
            ->add('status', $textType, ['template' => 'SonataInvoiceBundle:InvoiceAdmin:list_status.html.twig'])
            ->add('totalExcl', $currencyType, ['currency' => $this->currencyDetector->getCurrency()->getLabel()])
            ->add('totalInc', $currencyType, ['currency' => $this->currencyDetector->getCurrency()->getLabel()])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        // NEXT_MAJOR: Keep FQCN when bumping Symfony requirement to 2.8+.
        if (method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            $invoiceStatusType = 'Sonata\InvoiceBundle\Form\Type\InvoiceStatusType';
        } else {
            $invoiceStatusType = 'sonata_invoice_status';
        }

        $filter
            ->add('reference')
            ->add('customer')
            ->add('status', null, [], $invoiceStatusType, ['translation_domain' => $this->translationDomain])
        ;
    }
}
