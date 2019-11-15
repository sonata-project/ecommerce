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

namespace Sonata\OrderBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Component\Currency\CurrencyDetectorInterface;
use Sonata\Component\Currency\CurrencyFormType;
use Sonata\Component\Product\Pool;
use Sonata\OrderBundle\Form\Type\OrderStatusType;
use Sonata\ProductBundle\Form\Type\ProductDeliveryStatusType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormTypeInterface;

class OrderElementAdmin extends AbstractAdmin
{
    /**
     * @var CurrencyDetectorInterface
     */
    protected $currencyDetector;

    /**
     * @var Pool
     */
    protected $productPool;

    public function setCurrencyDetector(CurrencyDetectorInterface $currencyDetector)
    {
        $this->currencyDetector = $currencyDetector;
    }

    public function setProductPool(Pool $productPool)
    {
        $this->productPool = $productPool;
    }

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->parentAssociationMapping = 'order';
        $this->setTranslationDomain('SonataOrderBundle');
    }

    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $productTypeOptions = [
            'choices' => array_flip(array_keys($this->productPool->getProducts())),
        ];

        // choice_as_value option is not needed in SF 3.0+
        if (method_exists(FormTypeInterface::class, 'setDefaultOptions')) {
            $productTypeOptions['choices_as_values'] = true;
        }

        $formMapper
            ->with('order_element.form.group_main_label')
                ->add('productType', ChoiceType::class, $productTypeOptions)
                ->add('quantity')
                ->add('price')
                ->add('vatRate')
                ->add('designation')
                ->add('description', null, ['required' => false])
                ->add('status', OrderStatusType::class, ['translation_domain' => 'SonataOrderBundle'])
                ->add('deliveryStatus', ProductDeliveryStatusType::class, ['translation_domain' => 'SonataDeliveryBundle'])
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('id');

        if (!$list->getAdmin()->isChild()) {
            $list->add('order');
        }

        $list->add('productType')
            ->add('getStatusName', 'trans', ['name' => 'status', 'catalogue' => 'SonataOrderBundle', 'sortable' => 'status'])
            ->add('getDeliveryStatusName', 'trans', ['name' => 'deliveryStatus', 'catalogue' => 'SonataOrderBundle', 'sortable' => 'deliveryStatus'])
            ->add('getTotalWithVat', CurrencyFormType::class, [
                'currency' => $this->currencyDetector->getCurrency()->getLabel(),
            ])
            ->add('getTotal', CurrencyFormType::class, [
                'currency' => $this->currencyDetector->getCurrency()->getLabel(),
            ])
        ;
    }
}
