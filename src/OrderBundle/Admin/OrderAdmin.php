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

use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\Component\Currency\CurrencyDetectorInterface;
use Sonata\Component\Currency\CurrencyFormType;
use Sonata\Component\Invoice\InvoiceManagerInterface;
use Sonata\Component\Order\OrderManagerInterface;
use Sonata\CoreBundle\Form\Type\DatePickerType;
use Sonata\OrderBundle\Form\Type\OrderStatusType;
use Sonata\PaymentBundle\Form\Type\PaymentTransactionStatusType;
use Sonata\ProductBundle\Form\Type\ProductDeliveryStatusType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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
    public function setCurrencyDetector(CurrencyDetectorInterface $currencyDetector): void
    {
        $this->currencyDetector = $currencyDetector;
    }

    /**
     * @param InvoiceManagerInterface $invoiceManager
     */
    public function setInvoiceManager(InvoiceManagerInterface $invoiceManager): void
    {
        $this->invoiceManager = $invoiceManager;
    }

    /**
     * @param OrderManagerInterface $orderManager
     */
    public function setOrderManager(OrderManagerInterface $orderManager): void
    {
        $this->orderManager = $orderManager;
    }

    public function configure(): void
    {
        $this->parentAssociationMapping = 'customer';
        $this->setTranslationDomain('SonataOrderBundle');
    }

    public function configureFormFields(FormMapper $formMapper): void
    {
        // define group zoning
        $formMapper
             ->with('order.form.group_main_label', ['class' => 'col-md-12'])->end()
             ->with('order.form.group_billing_label', ['class' => 'col-md-6'])->end()
             ->with('order.form.group_shipping_label', ['class' => 'col-md-6'])->end()
        ;

        if (!$this->isChild()) {
            $formMapper
                ->with('order.form.group_main_label')
                    ->add('customer', ModelListType::class)
                ->end()
            ;
        }

        $formMapper
            ->with('order.form.group_main_label')
                ->add('currency', CurrencyFormType::class)
                ->add('locale', LocaleType::class)
                ->add('status', OrderStatusType::class, ['translation_domain' => 'SonataOrderBundle'])
                ->add('paymentStatus', PaymentTransactionStatusType::class, ['translation_domain' => 'SonataPaymentBundle'])
                ->add('deliveryStatus', ProductDeliveryStatusType::class, ['translation_domain' => 'SonataDeliveryBundle'])
                ->add('validatedAt', DatePickerType::class, ['dp_side_by_side' => true])
            ->end()
            ->with('order.form.group_billing_label', ['collapsed' => true])
                ->add('billingName')
                ->add('billingAddress1')
                ->add('billingAddress2')
                ->add('billingAddress3')
                ->add('billingCity')
                ->add('billingPostcode')
                ->add('billingCountryCode', CountryType::class)
                ->add('billingFax')
                ->add('billingEmail')
                ->add('billingMobile')
            ->end()
            ->with('order.form.group_shipping_label', ['collapsed' => true])
                ->add('shippingName')
                ->add('shippingAddress1')
                ->add('shippingAddress2')
                ->add('shippingAddress3')
                ->add('shippingCity')
                ->add('shippingPostcode')
                ->add('shippingCountryCode', CountryType::class)
                ->add('shippingFax')
                ->add('shippingEmail')
                ->add('shippingMobile')
            ->end()
        ;
    }

    public function configureListFields(ListMapper $list): void
    {
        $currency = $this->currencyDetector->getCurrency()->getLabel();

        $list
            ->addIdentifier('reference');

        if (!$list->getAdmin()->isChild()) {
            $list->addIdentifier('customer');
        }

        $list
            ->add('status', TextType::class, [
                'template' => '@SonataOrder/OrderAdmin/list_status.html.twig',
            ])
            ->add('deliveryStatus', TextType::class, [
                'template' => '@SonataOrder/OrderAdmin/list_delivery_status.html.twig',
            ])
            ->add('paymentStatus', TextType::class, [
                'template' => '@SonataOrder/OrderAdmin/list_payment_status.html.twig',
            ])
            ->add('validatedAt')
            ->add('totalInc', CurrencyFormType::class, ['currency' => $currency])
        ;
    }

    public function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('reference')
        ;

        if (!$this->isChild()) {
            $filter->add('customer.lastname');
        }

        $filter
            ->add('status', null, [], OrderStatusType::class, ['translation_domain' => $this->translationDomain])
            ->add('deliveryStatus', null, [], ProductDeliveryStatusType::class, ['translation_domain' => 'SonataDeliveryBundle'])
            ->add('paymentStatus', null, [], PaymentTransactionStatusType::class, ['translation_domain' => 'SonataPaymentBundle'])
        ;
    }

    public function configureRoutes(RouteCollection $collection): void
    {
        $collection->remove('create');
        $collection->add('generateInvoice');
    }

    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null): void
    {
        if (!$childAdmin && !\in_array($action, ['edit'])) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        $menu->addChild(
            'sonata.order.sidemenu.link_order_edit',
            ['uri' => $admin->generateUrl('edit', ['id' => $id])]
        );

        $menu->addChild(
            'sonata.order.sidemenu.link_order_elements_list',
            ['uri' => $admin->generateUrl('sonata.order.admin.order_element.list', ['id' => $id])]
        );

        $order = $this->orderManager->findOneBy(['id' => $id]);
        $invoice = $this->invoiceManager->findOneBy(['reference' => $order->getReference()]);

        if (null === $invoice) {
            $menu->addChild(
                'sonata.order.sidemenu.link_oRDER_TO_INVOICE_generate',
                ['uri' => $admin->generateUrl('generateInvoice', ['id' => $id])]
            );
        } else {
            $menu->addChild(
                'sonata.order.sidemenu.link_oRDER_TO_INVOICE_edit',
                ['uri' => $this->getConfigurationPool()->getAdminByAdminCode('sonata.invoice.admin.invoice')->generateUrl('edit', ['id' => $invoice->getId()])]
            );
        }
    }
}
