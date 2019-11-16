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
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ProductVariationAdmin extends ProductAdmin
{
    protected $parentAssociationMapping = 'parent';

    /**
     * Overwrite the default behavior to make ProductAdmin (product) > ProductAdmin (variation) works properly.
     *
     * @return string
     */
    public function getBaseRoutePattern()
    {
        if (!$this->baseRoutePattern) {
            if ('sonata.product.admin.product.variation' === $this->getCode() && !$this->isChild()) { // variation
                $this->baseRoutePattern = '/sonata/product/variation';
            } elseif ('sonata.product.admin.product.variation' === $this->getCode() && $this->isChild()) { // variation
                $this->baseRoutePattern = sprintf('%s/{id}/%s',
                    $this->getParent()->getBaseRoutePattern(),
                    $this->urlize('variation', '-')
                );
            } else {
                throw new \RuntimeException('Invalid method call due to invalid state');
            }
        }

        return $this->baseRoutePattern;
    }

    /**
     * Overwrite the default behavior to make ProductAdmin (product) > ProductAdmin (variation) works properly.
     *
     * @return string
     */
    public function getBaseRouteName()
    {
        if (!$this->baseRouteName) {
            if ('sonata.product.admin.product.variation' === $this->getCode() && !$this->isChild()) { // variation
                $this->baseRouteName = 'admin_sonata_product_variation';
            } elseif ('sonata.product.admin.product.variation' === $this->getCode() && $this->isChild()) { // variation
                $this->baseRouteName = sprintf('%s_%s',
                    $this->getParent()->getBaseRouteName(),
                    $this->urlize('variation')
                );
            } else {
                throw new \RuntimeException('Invalid method call due to invalid state');
            }
        }

        return $this->baseRouteName;
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

    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null): void
    {
        if (!$childAdmin && !\in_array($action, ['edit'], true)) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');
        $product = $this->getObject($id);

        $menu->addChild(
            'product.sidemenu.link_product_edit',
            ['uri' => $admin->generateUrl('edit', ['id' => $id])]
        );

        if (!$product->isVariation() && 'sonata.product.admin.product' === $this->getCode()) {
            $menu->addChild(
                'product.sidemenu.link_add_variation',
                ['uri' => $admin->generateUrl('sonata.product.admin.product.variation.create', ['id' => $id])]
            );

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
