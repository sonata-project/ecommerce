<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;

class DeliveryAdmin extends Admin
{
    /**
     * Overwrite the default behavior to make ProductAdmin (product) > ProductAdmin (delivery) works properly
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function getBaseRoutePattern()
    {
        if (!$this->baseRoutePattern) {
            if ($this->getCode() == 'sonata.product.admin.delivery' && !$this->isChild()) { // delivery
                $this->baseRoutePattern = '/sonata/product/delivery';
            } else if ($this->getCode() == 'sonata.product.admin.delivery' && $this->isChild()) { // delivery
                $this->baseRoutePattern = sprintf('%s/{id}/%s',
                    $this->getParent()->getBaseRoutePattern(),
                    $this->urlize('delivery', '-')
                );
            } else {
                throw new \RuntimeException('Invalid method call due to invalid state');
            }
        }

        return $this->baseRoutePattern;
    }

    /**
     * Overwrite the default behavior to make ProductAdmin (product) > ProductAdmin (delivery) works properly
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function getBaseRouteName()
    {
        if (!$this->baseRouteName) {
            if ($this->getCode() == 'sonata.product.admin.delivery' && !$this->isChild()) { // delivery
                $this->baseRouteName    = 'admin_sonata_product_delivery';
            } else if ($this->getCode() == 'sonata.product.admin.delivery' && $this->isChild()) { // delivery
                $this->baseRouteName = sprintf('%s_%s',
                    $this->getParent()->getBaseRouteName(),
                    $this->urlize('delivery')
                );
            } else {
                throw new \RuntimeException('Invalid method call due to invalid state');
            }
        }

        return $this->baseRouteName;
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
            $this->trans('product.sidemenu.link_product_edit', array(), 'SonataProductBundle'),
            array('uri' => $admin->generateUrl('edit', array('id' => $id)))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setTranslationDomain('SonataProductBundle');
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('enabled')
            ->add('code', 'sonata_delivery_choice')
            ->add('perItem')
            ->add('countryCode', 'country')
            ->add('zone')
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $list
     */
    public function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('code')
            ->add('enabled')
            ->add('perItem')
            ->add('countryCode')
            ->add('zone')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('code')
            ->add('countryCode')
        ;
    }


}
