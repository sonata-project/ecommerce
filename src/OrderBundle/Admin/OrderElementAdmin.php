<?php

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
use Sonata\Component\Product\Pool;

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

    /**
     * @param CurrencyDetectorInterface $currencyDetector
     */
    public function setCurrencyDetector(CurrencyDetectorInterface $currencyDetector)
    {
        $this->currencyDetector = $currencyDetector;
    }

    /**
     * @param Pool $productPool
     */
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
        // NEXT_MAJOR: Keep FQCN when bumping Symfony requirement to 2.8+.
        if (method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            $choiceType = 'Symfony\Component\Form\Extension\Core\Type\ChoiceType';
            $orderStatusType = 'Sonata\OrderBundle\Form\Type\OrderStatusType';
            $productDeliveryStatusType = 'Sonata\ProductBundle\Form\Type\ProductDeliveryStatusType';
        } else {
            $choiceType = 'choice';
            $orderStatusType = 'sonata_order_status';
            $productDeliveryStatusType = 'sonata_product_delivery_status';
        }

        $productTypeOptions = array();
        $productTypes = array_keys($this->productPool->getProducts());
        // NEXT_MAJOR: Remove this "if" (when requirement of Symfony is >= 2.7)
        if (method_exists('Symfony\Component\Form\AbstractType', 'configureOptions')) {
            $productTypes = array_flip($productTypes);
            // choice_as_value option is not needed in SF 3.0+
            if (method_exists('Symfony\Component\Form\FormTypeInterface', 'setDefaultOptions')) {
                $productTypeOptions['choices_as_values'] = true;
            }
        }
        $productTypeOptions['choices'] = $productTypes;

        $formMapper
            ->with($this->trans('order_element.form.group_main_label', array(), 'SonataOrderBundle'))
                ->add('productType', $choiceType, $productTypeOptions)
                ->add('quantity')
                ->add('price')
                ->add('vatRate')
                ->add('designation')
                ->add('description', null, array('required' => false))
                ->add('status', $orderStatusType, array('translation_domain' => 'SonataOrderBundle'))
                ->add('deliveryStatus', $productDeliveryStatusType, array('translation_domain' => 'SonataDeliveryBundle'))
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
            $currencyType = 'Sonata\Component\Currency\CurrencyFormType';
        } else {
            $currencyType = 'sonata_currency';
        }

        $list->addIdentifier('id');

        if (!$list->getAdmin()->isChild()) {
            $list->add('order');
        }

        $list->add('productType')
            ->add('getStatusName', 'trans', array('name' => 'status', 'catalogue' => 'SonataOrderBundle', 'sortable' => 'status'))
            ->add('getDeliveryStatusName', 'trans', array('name' => 'deliveryStatus', 'catalogue' => 'SonataOrderBundle', 'sortable' => 'deliveryStatus'))
            ->add('getTotalWithVat', $currencyType, array('currency' => $this->currencyDetector->getCurrency()->getLabel()))
            ->add('getTotal', $currencyType, array('currency' => $this->currencyDetector->getCurrency()->getLabel()))
        ;
    }
}
