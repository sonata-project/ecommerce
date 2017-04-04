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

use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\Component\Currency\CurrencyDetectorInterface;
use Sonata\Component\Invoice\InvoiceManagerInterface;
use Sonata\Component\Order\OrderManagerInterface;

class OrderAdmin extends AbstractAdmin
{
    /**
     * @var CurrencyDetectorInterface
     */
    protected $currencyDetector;

    /**
     * @var InvoiceManagerInterface
     */
    protected $invoiceManager;

    /**
     * @var OrderManagerInterface
     */
    protected $orderManager;

    /**
     * @param CurrencyDetectorInterface $currencyDetector
     */
    public function setCurrencyDetector(CurrencyDetectorInterface $currencyDetector)
    {
        $this->currencyDetector = $currencyDetector;
    }

    /**
     * @param InvoiceManagerInterface $invoiceManager
     */
    public function setInvoiceManager(InvoiceManagerInterface $invoiceManager)
    {
        $this->invoiceManager = $invoiceManager;
    }

    /**
     * @param OrderManagerInterface $orderManager
     */
    public function setOrderManager(OrderManagerInterface $orderManager)
    {
        $this->orderManager = $orderManager;
    }

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->parentAssociationMapping = 'customer';
        $this->setTranslationDomain('SonataOrderBundle');
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
            $localeType = 'Symfony\Component\Form\Extension\Core\Type\LocaleType';
            $orderStatusType = 'Sonata\OrderBundle\Form\Type\OrderStatusType';
            $paymentTransactionStatusType = 'Sonata\PaymentBundle\Form\Type\PaymentTransactionStatusType';
            $productDeliveryStatusType = 'Sonata\ProductBundle\Form\Type\ProductDeliveryStatusType';
            $datePickerType = 'Sonata\CoreBundle\Form\Type\DatePickerType';
            $countryType = 'Symfony\Component\Form\Extension\Core\Type\CountryType';
        } else {
            $modelListType = 'sonata_type_model_list';
            $currencyType = 'sonata_currency';
            $localeType = 'locale';
            $orderStatusType = 'sonata_order_status';
            $paymentTransactionStatusType = 'sonata_payment_transaction_status';
            $productDeliveryStatusType = 'sonata_product_delivery_status';
            $datePickerType = 'sonata_type_datetime_picker';
            $countryType = 'country';
        }

        // define group zoning
        $formMapper
             ->with($this->trans('order.form.group_main_label'), array('class' => 'col-md-12'))->end()
             ->with($this->trans('order.form.group_billing_label'), array('class' => 'col-md-6'))->end()
             ->with($this->trans('order.form.group_shipping_label'), array('class' => 'col-md-6'))->end()
        ;

        if (!$this->isChild()) {
            $formMapper
                ->with($this->trans('order.form.group_main_label', array(), 'SonataOrderBundle'))
                    ->add('customer', $modelListType)
                ->end()
            ;
        }

        $formMapper
            ->with($this->trans('order.form.group_main_label', array(), 'SonataOrderBundle'))
                ->add('currency', $currencyType)
                ->add('locale', $localeType)
                ->add('status', $orderStatusType, array('translation_domain' => 'SonataOrderBundle'))
                ->add('paymentStatus', $paymentTransactionStatusType, array('translation_domain' => 'SonataPaymentBundle'))
                ->add('deliveryStatus', $productDeliveryStatusType, array('translation_domain' => 'SonataDeliveryBundle'))
                ->add('validatedAt', $datePickerType, array('dp_side_by_side' => true))
            ->end()
            ->with($this->trans('order.form.group_billing_label', array(), 'SonataOrderBundle'), array('collapsed' => true))
                ->add('billingName')
                ->add('billingAddress1')
                ->add('billingAddress2')
                ->add('billingAddress3')
                ->add('billingCity')
                ->add('billingPostcode')
                ->add('billingCountryCode', $countryType)
                ->add('billingFax')
                ->add('billingEmail')
                ->add('billingMobile')
            ->end()
            ->with($this->trans('order.form.group_shipping_label', array(), 'SonataOrderBundle'), array('collapsed' => true))
                ->add('shippingName')
                ->add('shippingAddress1')
                ->add('shippingAddress2')
                ->add('shippingAddress3')
                ->add('shippingCity')
                ->add('shippingPostcode')
                ->add('shippingCountryCode', $countryType)
                ->add('shippingFax')
                ->add('shippingEmail')
                ->add('shippingMobile')
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

        $currency = $this->currencyDetector->getCurrency()->getLabel();

        $list
            ->addIdentifier('reference');

        if (!$list->getAdmin()->isChild()) {
            $list->addIdentifier('customer');
        }

        $list
            ->add('status', $textType, array('template' => 'SonataOrderBundle:OrderAdmin:list_status.html.twig'))
            ->add('deliveryStatus', $textType, array('template' => 'SonataOrderBundle:OrderAdmin:list_delivery_status.html.twig'))
            ->add('paymentStatus', $textType, array('template' => 'SonataOrderBundle:OrderAdmin:list_payment_status.html.twig'))
            ->add('validatedAt')
            ->add('totalInc', $currencyType, array('currency' => $currency))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureDatagridFilters(DatagridMapper $filter)
    {
        // NEXT_MAJOR: Keep FQCN when bumping Symfony requirement to 2.8+.
        if (method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            $orderStatusType = 'Sonata\OrderBundle\Form\Type\OrderStatusType';
            $productDeliveryStatusType = 'Sonata\ProductBundle\Form\Type\ProductDeliveryStatusType';
            $paymentTransactionStatusType = 'Sonata\PaymentBundle\Form\Type\PaymentTransactionStatusType';
        } else {
            $orderStatusType = 'sonata_order_status';
            $productDeliveryStatusType = 'sonata_product_delivery_status';
            $paymentTransactionStatusType = 'sonata_payment_transaction_status';
        }

        $filter
            ->add('reference')
        ;

        if (!$this->isChild()) {
            $filter->add('customer.lastname');
        }

        $filter
            ->add('status', null, array(), $orderStatusType, array('translation_domain' => $this->translationDomain))
            ->add('deliveryStatus', null, array(), $productDeliveryStatusType, array('translation_domain' => 'SonataDeliveryBundle'))
            ->add('paymentStatus', null, array(), $paymentTransactionStatusType, array('translation_domain' => 'SonataPaymentBundle'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
        $collection->add('generateInvoice');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, array('edit'))) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        $menu->addChild(
            $this->trans('sonata.order.sidemenu.link_order_edit', array(), 'SonataOrderBundle'),
            array('uri' => $admin->generateUrl('edit', array('id' => $id)))
        );

        $menu->addChild(
            $this->trans('sonata.order.sidemenu.link_order_elements_list', array(), 'SonataOrderBundle'),
            array('uri' => $admin->generateUrl('sonata.order.admin.order_element.list', array('id' => $id)))
        );

        $order = $this->orderManager->findOneBy(array('id' => $id));
        $invoice = $this->invoiceManager->findOneBy(array('reference' => $order->getReference()));

        if (null === $invoice) {
            $menu->addChild(
                $this->trans('sonata.order.sidemenu.link_oRDER_TO_INVOICE_generate', array(), 'SonataOrderBundle'),
                array('uri' => $admin->generateUrl('generateInvoice', array('id' => $id)))
            );
        } else {
            $menu->addChild(
                $this->trans('sonata.order.sidemenu.link_oRDER_TO_INVOICE_edit', array(), 'SonataOrderBundle'),
                array('uri' => $this->getConfigurationPool()->getAdminByAdminCode('sonata.invoice.admin.invoice')->generateUrl('edit', array('id' => $invoice->getId())))
            );
        }
    }
}
