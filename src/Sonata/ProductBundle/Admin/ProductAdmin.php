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
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\Component\Product\Pool;

class ProductAdmin extends Admin
{
    protected $pool;

    /**
     * @param $code
     * @param $class
     * @param $baseControllerName
     * @param \Sonata\Component\Product\Pool $pool
     */
    public function __construct($code, $class, $baseControllerName, Pool $pool)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->pool = $pool;
    }


    public function configure()
    {
        $this->setTranslationDomain('SonataProductBundle');
    }

    /**
     * @return string
     */
    public function getClass()
    {
        if ($this->hasSubject()) {
            return get_class($this->getSubject());
        } else if($class = $this->getProductClass()) {
            return $class;
        }

        return parent::getClass();
    }

    public function getProductType()
    {
        return $this->getRequest()->get('provider');
    }

    /**
     * Returns the product class from the provided request
     * @return string
     */
    public function getProductClass()
    {
        if($this->hasRequest()) {
            $code = $this->getProductType();

            if ($code) {
                return $this->pool->getManager($code)->getClass();
            }
        }

        return null;
    }

    /**
     * Returns the subject, if none is set try to load one from the request
     *
     * @return object $object the subject
     */
    public function getSubject()
    {
        if ($this->subject === null && $this->hasRequest()) {
            $id = $this->request->get($this->getIdParameter());
            if (!is_numeric($id)) {
                $this->subject = false;
            } else {
                $this->subject = $this->getModelManager()->find($this->getProductClass(), $id);
            }
        }

        return $this->subject;
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $product = $this->getSubject();

        if (!$product) {
            $product = $this->getNewInstance();
        }

        $provider = $this->pool->getProvider($product);

        if ($product->getId() > 0) {
            $provider->buildEditForm($formMapper);
        } else {
            $provider->buildCreateForm($formMapper);
        }
    }

    /**
     * @return array
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
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $list
     * @return void
     */
    public function configureListFields(ListMapper $list)
    {
        $list
            ->add('enabled')
            ->addIdentifier('name')
            ->add('price')
            ->add('stock')
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $filter
     * @return void
     */
    public function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('name')
//            ->add('price')
            ->add('enabled')
        ;
    }

    /**
     * @param \Knp\Menu\ItemInterface $menu
     * @param $action
     * @param null|\Sonata\AdminBundle\Admin\Admin $childAdmin
     * @return
     */
    public function configureSideMenu(MenuItemInterface $menu, $action, Admin $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, array('edit'))) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        $menu->addChild(
            $this->trans('sidemenu.link_product_edit'),
            array('uri' => $admin->generateUrl('edit', array('id' => $id)))
        );

        $menu->addChild(
            $this->trans('sidemenu.link_category_list'),
            array('uri' => $admin->generateUrl('sonata.product.admin.category.list', array('id' => $id)))
        );

//        $menu->addChild(
//            $this->trans('link_variation_list', array(), 'SonataProductBundle'),
//            array('uri' => $admin->generateUrl('sonata.product.admin.variation.list', array('id' => $id)))
//        );

        $menu->addChild(
            $this->trans('sidemenu.link_delivery_list'),
            array('uri' => $admin->generateUrl('sonata.product.admin.delivery.list', array('id' => $id)))
        );
    }
}
