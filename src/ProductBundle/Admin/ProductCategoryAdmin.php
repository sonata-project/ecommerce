<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ProductCategoryAdmin extends Admin
{
    protected $parentAssociationMapping = 'product';

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setTranslationDomain('SonataProductBundle');

        $this->baseRouteName    = 'admin_sonata_product_productcategory';
        $this->baseRoutePattern = '/sonata/product/productcategory';
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
                    'admin_code' => 'sonata.product.admin.product',
                ));
        }

        $list
            ->addIdentifier('category')
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $filter
     */
    public function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('category')
        ;
    }
}
