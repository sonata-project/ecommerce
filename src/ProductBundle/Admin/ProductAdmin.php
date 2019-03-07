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

namespace Sonata\ProductBundle\Admin;

use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Component\Currency\CurrencyDetectorInterface;
use Sonata\Component\Currency\CurrencyFormType;
use Sonata\Component\Product\Pool;
use Sonata\Component\Product\ProductInterface;
use Sonata\CoreBundle\Form\Type\BooleanType;
use Sonata\CoreBundle\Validator\ErrorElement;

class ProductAdmin extends AbstractAdmin
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
    public function setCurrencyDetector(CurrencyDetectorInterface $currencyDetector): void
    {
        $this->currencyDetector = $currencyDetector;
    }

    public function configure(): void
    {
        $this->setTranslationDomain('SonataProductBundle');
    }

    /**
     * @return string
     */
    public function getProductType()
    {
        return $this->getRequest()->get('provider');
    }

    public function configureFormFields(FormMapper $formMapper): void
    {
        // this admin class works only from a request scope
        if (!$this->hasRequest()) {
            return;
        }

        $product = $this->getProduct();
        $provider = $this->getProductProvider($product);

        if ($product->getId() > 0) {
            $provider->buildEditForm($formMapper, $product->isVariation());
        } else {
            $provider->buildCreateForm($formMapper);
        }
    }

    public function getPersistentParameters()
    {
        if (!$this->hasRequest()) {
            return [];
        }

        return [
            'provider' => $this->getProductType(),
        ];
    }

    public function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('sku')
            ->addIdentifier('name')
            ->add('isVariation', BooleanType::class)
            ->add('enabled', null, ['editable' => true])
            ->add('price', CurrencyFormType::class, [
                'currency' => $this->currencyDetector->getCurrency()->getLabel(),
            ])
            ->add('productCategories', null, ['associated_tostring' => 'getCategory'])
            ->add('productCollections', null, ['associated_tostring' => 'getCollection'])
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $filter
     */
    public function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('name')
            ->add('sku')
            ->add('enabled')
            ->add('productCategories.category', null, ['field_options' => ['expanded' => false, 'multiple' => true]])
            ->add('productCollections.collection', null, ['field_options' => ['expanded' => false, 'multiple' => true]])
        ;
    }

    /**
     * @param \Sonata\Component\Product\Pool $productPool
     */
    public function setProductPool(Pool $productPool): void
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

    public function getClass()
    {
        if ($this->hasRequest() && $code = $this->getProductType()) {
            return $this->getProductPool()->getManager($code)->getClass();
        }

        return parent::getClass();
    }

    public function validate(ErrorElement $errorElement, $object): void
    {
        $errorElement
            ->assertCallback(['callback' => 'validateOneMainCategory'])
        ;
    }

    public function postUpdate($product): void
    {
        $provider = $this->getProductProvider($product);

        if ($product->hasVariations()) {
            $provider->synchronizeVariations($product);
        }
    }

    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null): void
    {
        if (!$childAdmin && !\in_array($action, ['edit'])) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');
        $product = $this->getObject($id);

        $menu->addChild(
            'product.sidemenu.link_product_edit',
            ['uri' => $admin->generateUrl('edit', ['id' => $id])]
        );

        $menu->addChild(
            'product.sidemenu.view_categories',
            ['uri' => $admin->generateUrl('sonata.product.admin.product.category.list', ['id' => $id])]
        );

        $menu->addChild(
            'product.sidemenu.view_collections',
            ['uri' => $admin->generateUrl('sonata.product.admin.product.collection.list', ['id' => $id])]
        );

        $menu->addChild(
            'product.sidemenu.view_deliveries',
            ['uri' => $admin->generateUrl('sonata.product.admin.delivery.list', ['id' => $id])]
        );

        if (!$product->isVariation() && 'sonata.product.admin.product' == $this->getCode()) {
            $menu->addChild(
                'product.sidemenu.view_variations',
                ['uri' => $admin->generateUrl('sonata.product.admin.product.variation.list', ['id' => $id])]
            );
        }
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        // this admin class works only from a request scope
        if (!$this->hasRequest()) {
            return;
        }

        if ($this->isChild()) { // variation
            return;
        }

        $product = $this->getProduct();
        $provider = $this->getProductProvider($product);

        $provider->configureShowFields($showMapper);
    }
}
