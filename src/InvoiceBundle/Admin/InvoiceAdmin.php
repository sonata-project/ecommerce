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

namespace Sonata\InvoiceBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\Component\Currency\CurrencyDetectorInterface;
use Sonata\Component\Currency\CurrencyFormType;
use Sonata\InvoiceBundle\Form\Type\InvoiceStatusType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class InvoiceAdmin extends AbstractAdmin
{
    /**
     * @var CurrencyDetectorInterface
     */
    protected $currencyDetector;

    /**
     * @param CurrencyDetectorInterface $currencyDetector
     */
    public function setCurrencyDetector(CurrencyDetectorInterface $currencyDetector): void
    {
        $this->currencyDetector = $currencyDetector;
    }

    public function configure(): void
    {
        $this->setTranslationDomain('SonataInvoiceBundle');
    }

    public function configureFormFields(FormMapper $formMapper): void
    {
        if (!$this->isChild()) {
            $formMapper
                ->with('invoice.form.group_main_label')
                    ->add('customer', ModelListType::class)
                ->end()
            ;
        }

        $formMapper
            ->with('invoice.form.group_main_label')
                ->add('reference')
                ->add('currency', CurrencyFormType::class)
                ->add('status', InvoiceStatusType::class, ['translation_domain' => $this->translationDomain])
                ->add('totalExcl')
                ->add('totalInc')
            ->end()
            ->with('invoice.form.group_billing_label', ['collapsed' => true])
                ->add('name')
                ->add('phone')
                ->add('address1')
                ->add('address2')
                ->add('address3')
                ->add('city')
                ->add('postcode')
                ->add('country', CountryType::class)
                ->add('fax')
                ->add('email')
                ->add('mobile')
            ->end()
        ;
    }

    public function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('reference')
            ->add('customer')
            ->add('status', TextType::class, [
                'template' => '@SonataInvoice/InvoiceAdmin/list_status.html.twig',
            ])
            ->add('totalExcl', CurrencyFormType::class, [
                'currency' => $this->currencyDetector->getCurrency()->getLabel(),
            ])
            ->add('totalInc', CurrencyFormType::class, [
                'currency' => $this->currencyDetector->getCurrency()->getLabel(),
            ])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('reference')
            ->add('customer')
            ->add('status', null, [], InvoiceStatusType::class, ['translation_domain' => $this->translationDomain])
        ;
    }
}
