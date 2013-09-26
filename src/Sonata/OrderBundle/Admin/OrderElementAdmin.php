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

use Sonata\Component\Currency\CurrencyDetectorInterface;

class OrderElementAdmin extends Admin
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
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->parentAssociationMapping = 'order';
        $this->setTranslationDomain('SonataOrderBundle');
    }

    /**
     * {@inheritDoc}
     */
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
                ->add('status', 'sonata_order_status', array('translation_domain' => 'SonataOrderBundle'))
                ->add('deliveryStatus', 'sonata_product_delivery_status', array('translation_domain' => 'SonataDeliveryBundle'))
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
            ->add('order')
            ->add('productType')
            ->add('getStatusName', 'trans', array('name' => 'status', 'catalogue' => 'SonataOrderBundle', 'stortable' => 'status'))
            ->add('getDeliveryStatusName', 'trans', array('name' => 'deliveryStatus', 'catalogue' => 'SonataOrderBundle', 'stortable' => 'deliveryStatus'))
            ->add('getTotalWithVat', 'currency', array('currency' => $this->currencyDetector->getCurrency()->getLabel()))
            ->add('getTotal', 'currency', array('currency' => $this->currencyDetector->getCurrency()->getLabel()))
        ;
    }
}
