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
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\Component\Form\Type\DeliveryChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;

class DeliveryAdmin extends AbstractAdmin
{
    protected $parentAssociationMapping = 'product';

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setTranslationDomain('SonataProductBundle');
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        if (!$this->isChild()) {
            $formMapper->add('product', ModelListType::class, [], [
                'admin_code' => 'sonata.product.admin.product',
            ]);
        }

        $formMapper
            ->add('enabled')
            ->add('code', DeliveryChoiceType::class)
            ->add('perItem')
            ->add('countryCode', CountryType::class)
            ->add('zone')
        ;
    }

    public function configureListFields(ListMapper $list)
    {
        if (!$this->isChild()) {
            $list
                ->addIdentifier('id')
                ->addIdentifier('product', null, [
                    'admin_code' => 'sonata.product.admin.product',
                ]);
        }

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
    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && !\in_array($action, ['edit'], true)) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        $menu->addChild(
            'product.sidemenu.link_product_edit',
            ['uri' => $admin->generateUrl('edit', ['id' => $id])]
        );
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
