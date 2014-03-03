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
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\Component\Product\Pool;
use Knp\Menu\ItemInterface as MenuItemInterface;

class ProductCategoryAdmin extends Admin
{
    protected $parentAssociationMapping = 'product';

    /**
     * Overwrite the default behavior to make ProductAdmin (product) > ProductAdmin (category) works properly
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function getBaseRoutePattern()
    {
        if (!$this->baseRoutePattern) {
            if ($this->getCode() == 'sonata.product.admin.product.category' && !$this->isChild()) { // category
                $this->baseRoutePattern = '/sonata/product/category';
            } else if ($this->getCode() == 'sonata.product.admin.product.category' && $this->isChild()) { // category
                $this->baseRoutePattern = sprintf('%s/{id}/%s',
                    $this->getParent()->getBaseRoutePattern(),
                    $this->urlize('category', '-')
                );
            } else {
                throw new \RuntimeException('Invalid method call due to invalid state');
            }
        }

        return $this->baseRoutePattern;
    }

    /**
     * Overwrite the default behavior to make ProductAdmin (product) > ProductAdmin (collection) works properly
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function getBaseRouteName()
    {
        if (!$this->baseRouteName) {
            if ($this->getCode() == 'sonata.product.admin.product.category' && !$this->isChild()) { // category
                $this->baseRouteName    = 'admin_sonata_product_category';
            } else if ($this->getCode() == 'sonata.product.admin.product.category' && $this->isChild()) { // category
                $this->baseRouteName = sprintf('%s_%s',
                    $this->getParent()->getBaseRouteName(),
                    $this->urlize('category')
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
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('category')
            ->add('main')
            ->add('enabled')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureListFields(ListMapper $list)
    {
        if (!$this->isChild()) {
            $list
                ->addIdentifier('id')
                ->addIdentifier('product', null, array(
                    'admin_code' => 'sonata.product.admin.product'
                ));
        }

        $list
            ->addIdentifier('category')
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $filter
     *
     * @return void
     */
    public function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('category')
        ;
    }
}
