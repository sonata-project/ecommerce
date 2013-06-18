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
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\Component\Product\Pool;
use Sonata\FormatterBundle\Formatter\Pool as FormatterPool;

class ProductAdmin extends Admin
{
    /**
     * @var \Sonata\Component\Product\Pool
     */
    protected $productPool;

    /**
     * @var \Sonata\FormatterBundle\Formatter\Pool
     */
    protected $poolFormatter;

    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setTranslationDomain('SonataProductBundle');

        $this->baseRouteName    = 'admin_sonata_product_product';
        $this->baseRoutePattern = '/sonata/product/product';
    }

    /**
     * @return string
     */
    public function getClass()
    {
        if ($this->hasSubject()) {
            return get_class($this->getSubject());
        } elseif ($class = $this->getProductClass()) {
            return $class;
        }

        return parent::getClass();
    }

    /**
     * @return string
     */
    public function getProductType()
    {
        return $this->getRequest()->get('provider');
    }

    /**
     * Returns the product class from the provided request.
     *
     * @return string
     */
    public function getProductClass()
    {
        if ($this->hasRequest()) {
            $code = $this->getProductType();

            if ($code) {
                return $this->getProductPool()->getManager($code)->getClass();
            }
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        // this admin class works only from a request scope
        if (!$this->hasRequest()) {
            return;
        }

        $product = $this->getSubject();

        if (!$product) {
            $product = $this->getNewInstance();
        }

        $provider = $this->getProductPool()->getProvider($product);

        if ($product->getId() > 0) {
            $provider->buildEditForm($formMapper);
        } else {
            $provider->buildCreateForm($formMapper);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getPersistentParameters()
    {
        if (!$this->hasRequest()) {
            return array();
        }

        return array(
            'provider' => $this->getProductType(),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function configureListFields(ListMapper $list)
    {
        $list
            ->add('enabled')
            ->addIdentifier('name')
            ->add('price', 'currency', array('currency' => 'EUR')) // for now the currency is not handled
            ->add('stock')
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
            ->add('name')
            ->add('enabled')
        ;
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
            $this->trans('sidemenu.link_product_edit', array(), 'SonataProductBundle'),
            array('uri' => $admin->generateUrl('edit', array('id' => $id)))
        );

        $menu->addChild(
            $this->trans('sidemenu.link_category_list', array(), 'SonataProductBundle'),
            array('uri' => $admin->generateUrl('sonata.product.admin.category.list', array('id' => $id)))
        );

        $menu->addChild(
            $this->trans('sidemenu.link_delivery_list', array(), 'SonataProductBundle'),
            array('uri' => $admin->generateUrl('sonata.product.admin.delivery.list', array('id' => $id)))
        );
    }

    /**
     * @param \Sonata\Component\Product\Pool $productPool
     */
    public function setProductPool(Pool $productPool)
    {
        $this->productPool = $productPool;
    }

    /**
     * @return \Sonata\Component\Product\Pool
     */
    public function getProductPool()
    {
        return $this->productPool;
    }

    /**
     * @param \Sonata\FormatterBundle\Formatter\Pool $formatterPool
     *
     * @return void
     */
    public function setPoolFormatter(FormatterPool $formatterPool)
    {
        $this->formatterPool = $formatterPool;
    }

    /**
     * @return \Sonata\FormatterBundle\Formatter\Pool
     */
    public function getPoolFormatter()
    {
        return $this->formatterPool;
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($product)
    {
        $product->setDescription($this->getPoolFormatter()->transform($product->getDescriptionFormatter(), $product->getRawDescription()));
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($product)
    {
        $product->setDescription($this->getPoolFormatter()->transform($product->getDescriptionFormatter(), $product->getRawDescription()));
    }
}
