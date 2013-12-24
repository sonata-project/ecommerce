<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Menu;

use Sonata\SeoBundle\Menu\BaseBreadcrumbMenuBuilder;
use Sonata\SeoBundle\Menu\BreadcrumbMenuBuilderInterface;

/**
 * Breadcrumb menu builder for the product catalog.
 *
 * @author Sylvain Deloux <sylvain.deloux@fullsix.com>
 */
class CatalogBreadcrumbMenuBuilder extends BaseBreadcrumbMenuBuilder implements BreadcrumbMenuBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBreadcrumbMenu($parameters = array())
    {
        $menu = $this->getRootMenu();

        $menu->addChild('Catalog',
            array(
                'route' => 'sonata_catalog_index',
            )
        );

        $categories = array();
        $product    = array();

        if ($category = $parameters['category']) {
            $sorted = array($category);

            while ($c = $category->getParent()) {
                $sorted[] = $c;
                $category = $c;
            }

            $categories = array_reverse($sorted, true);
        }

        if ($product = $parameters['product']) {
            $category = $product->getMainCategory();

            $sorted = array($category);

            while ($c = $category->getParent()) {
                $sorted[] = $c;
                $category = $c;
            }

            $category = null;

            $categories = array_reverse($sorted, true);
        }

        if (count($categories) > 0) {
            foreach ($categories as $category) {
                $menu->addChild($category->getName(), array(
                    'route'           => 'sonata_catalog_category',
                    'routeParameters' => array(
                        'category_id'   => $category->getId(),
                        'category_slug' => $category->getSlug(),
                    ),
                ));
            }
        }

        if ($product) {
            $menu->addChild($product->getName(), array(
                'route'           => 'sonata_product_view',
                'routeParameters' => array(
                    'productId' => $product->getId(),
                    'slug'      => $product->getSlug(),
                ),
            ));
        }

        return $menu;
    }
}
