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
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\Component\Product\Pool;
use Sonata\Component\Product\ProductInterface;
use Sonata\Component\Currency\CurrencyDetectorInterface;
use Knp\Menu\ItemInterface as MenuItemInterface;

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
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('createVariation', $this->getRouterIdParameter() . '/variation/create')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        // this admin class works only from a request scope
        if (!$this->hasRequest()) {
            return;
        }

        $product  = $this->getProduct();
        $provider = $this->getProductProvider($product);

        if ($product->getId() > 0) {
            $provider->buildEditForm($formMapper, $product->isVariation());
        } else {
            $provider->buildCreateForm($formMapper);
        }
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

        $id      = $admin->getRequest()->get('id');
        $product = $this->getObject($id);

        $menu->addChild(
            $this->trans('product.sidemenu.link_product_edit', array(), 'SonataProductBundle'),
            array('uri' => $admin->generateUrl('edit', array('id' => $id)))
        );

        if (!$product->isVariation()) {
            $menu->addChild(
                $this->trans('product.sidemenu.link_add_variation', array(), 'SonataProductBundle'),
                array('uri' => $admin->generateUrl('createVariation', array('id' => $id)))
            );
        }
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        // this admin class works only from a request scope
        if (!$this->hasRequest()) {
            return;
        }

        $product  = $this->getProduct();
        $provider = $this->getProductProvider($product);

        $provider->configureShowFields($showMapper);
    }

    /**
     * {@inheritdoc}
     */
    public function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('name')
            ->add('enabled', null, array(
                'editable'     => true,
                'confirmation' => true,
            ))
            ->add('price', 'currency', array('currency' => $this->currencyDetector->getCurrency()->getLabel()))
            ->add('productCategories', null, array('associated_tostring' => 'getCategory'))
            ->add('productCollections', null, array('associated_tostring' => 'getCollection'))
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
            ->add('sku')
            ->add('enabled')
            ->add('productCategories.category', null, array('field_options' => array('expanded' => true, 'multiple' => true)))
            ->add('productCollections.collection', null, array('field_options' => array('expanded' => true, 'multiple' => true)))
        ;
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
     * Return the Product Provider.
     *
     * @param ProductInterface $product
     *
     * @return \Sonata\Component\Product\ProductProviderInterface
     */
    public function getProductProvider(ProductInterface $product)
    {
        return $this->getProductPool()->getProvider($product);
    }

    /**
     * Return the current Product.
     *
     * @return ProductInterface
     */
    public function getProduct()
    {
        $product = $this->getSubject();

        if (!$product) {
            $product = $this->getNewInstance();
        }

        return $product;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->assertCallback(array('validateOneMainCategory'))
        ;
    }
}
