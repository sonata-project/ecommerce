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
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;

use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\Component\Currency\CurrencyDetectorInterface;

class OrderAdmin extends Admin
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
        $this->parentAssociationMapping = 'customer';
        $this->setTranslationDomain('SonataOrderBundle');
    }

    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        if (!$this->isChild()) {
            $formMapper
                ->with($this->trans('order.form.group_main_label', array(), 'SonataOrderBundle'))
                    ->add('customer', 'sonata_type_model_list')
                ->end()
            ;
        }

        $formMapper
            ->with($this->trans('order.form.group_main_label', array(), 'SonataOrderBundle'))
                ->add('currency', 'sonata_currency')
                ->add('locale', 'locale')
                ->add('status', 'sonata_order_status', array('translation_domain' => 'SonataOrderBundle'))
                ->add('paymentStatus', 'sonata_payment_transaction_status', array('translation_domain' => 'SonataPaymentBundle'))
                ->add('deliveryStatus', 'sonata_product_delivery_status', array('translation_domain' => 'SonataDeliveryBundle'))
                ->add('validatedAt')
            ->end()
            ->with($this->trans('order.form.group_billing_label', array(), 'SonataOrderBundle'), array('collapsed' => true))
                ->add('billingName')
                ->add('billingAddress1')
                ->add('billingAddress2')
                ->add('billingAddress3')
                ->add('billingCity')
                ->add('billingPostcode')
                ->add('billingCountryCode', 'country')
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
                ->add('shippingCountryCode', 'country')
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
        $list
            ->addIdentifier('id')
            ->addIdentifier('reference')
            ->add('customer')
            ->add('locale')
            ->add('getStatusName', 'trans', array('name' => 'status', 'catalogue' => 'SonataOrderBundle', 'sortable' => 'status'))
            ->add('getDeliveryStatusName', 'trans', array('name' => 'deliveryStatus', 'catalogue' => 'SonataDeliveryBundle', 'sortable' => 'deliveryStatus'))
            ->add('getPaymentStatusName', 'trans', array('name' => 'paymentStatus', 'catalogue' => 'SonataPaymentBundle', 'sortable' => 'paymentStatus'))
            ->add('validatedAt')
            ->add('totalExcl', 'currency', array('currency' => $this->currencyDetector->getCurrency()->getLabel()))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('reference')
        ;

        if (!$this->isChild()) {
            $filter->add('customer.lastname');
        }

        $filter
            ->add('locale', null, array(), 'country')
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
            $this->trans('order.sidemenu.link_order_edit', array(), 'SonataOrderBundle'),
            array('uri' => $admin->generateUrl('edit', array('id' => $id)))
        );

        $menu->addChild(
            $this->trans('order.sidemenu.link_order_elements_list', array(), 'SonataOrderBundle'),
            array('uri' => $admin->generateUrl('sonata.order.admin.order_element.list', array('id' => $id)))
        );

        $menu->addChild(
            $this->trans('order.sidemenu.link_order_invoice_generate', array(), 'SonataOrderBundle'),
            array('uri' => $admin->generateUrl('generateInvoice', array('id' => $id)))
        );
    }
}
