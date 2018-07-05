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

namespace Sonata\ProductBundle\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\SeoBundle\Block\Breadcrumb\BaseBreadcrumbMenuBlockService;

/**
 * BlockService for product catalog breadcrumb.
 *
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
class CatalogBreadcrumbBlockService extends BaseBreadcrumbMenuBlockService
{
    public function getName()
    {
        return 'sonata.product.block.breadcrumb';
    }

    protected function getMenu(BlockContextInterface $blockContext)
    {
        $menu = $this->getRootMenu($blockContext);

        $menu->addChild('sonata_product_catalog_breadcrumb', [
            'route' => 'sonata_catalog_index',
            'extras' => ['translation_domain' => 'SonataProductBundle'],
        ]);

        $categories = [];
        $product = null;

        if ($category = $blockContext->getBlock()->getSetting('category')) {
            $sorted = [$category];

            while ($c = $category->getParent()) {
                $sorted[] = $c;
                $category = $c;
            }

            $categories = array_reverse($sorted, true);
        }

        if ($product = $blockContext->getBlock()->getSetting('product')) {
            if ($category = $product->getMainCategory()) {
                $sorted = [$category];

                while ($c = $category->getParent()) {
                    $sorted[] = $c;
                    $category = $c;
                }

                $category = null;

                $categories = array_reverse($sorted, true);
            }
        }

        if (count($categories) > 0) {
            foreach ($categories as $category) {
                $menu->addChild($category->getName(), [
                    'route' => 'sonata_catalog_category',
                    'routeParameters' => [
                        'category_id' => $category->getId(),
                        'category_slug' => $category->getSlug(),
                    ],
                ]);
            }
        }

        if ($product) {
            $menu->addChild($product->getName(), [
                'route' => 'sonata_product_view',
                'routeParameters' => [
                    'productId' => $product->getId(),
                    'slug' => $product->getSlug(),
                ],
            ]);
        }

        return $menu;
    }
}
