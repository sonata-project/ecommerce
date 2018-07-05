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

namespace Sonata\ProductBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\Component\Product\ProductCategoryManagerInterface;
use Sonata\Component\Product\ProductProviderInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class ProductMenuBuilder
{
    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @var ProductCategoryManagerInterface
     */
    protected $categoryManager;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @param FactoryInterface                $factory
     * @param ProductCategoryManagerInterface $categoryManager
     * @param RouterInterface                 $router
     */
    public function __construct(FactoryInterface $factory, ProductCategoryManagerInterface $categoryManager, RouterInterface $router)
    {
        $this->factory = $factory;
        $this->categoryManager = $categoryManager;
        $this->router = $router;
    }

    /**
     * Generates the filters menu based on $productProvider.
     *
     * @param ProductProviderInterface $productProvider
     * @param array                    $itemOptions
     * @param string                   $currentUri
     *
     * @return mixed
     */
    public function createFiltersMenu(ProductProviderInterface $productProvider, array $itemOptions = [], $currentUri = null)
    {
        $menu = $this->factory->createItem('filters', $itemOptions);

        $filters = $productProvider->getFilters();

        foreach ($filters as $filter => $options) {
            $menuItem = $menu->addChild($filter, array_merge(['attributes' => ['class' => 'nav-header']], $itemOptions));

            foreach ($options as $option) {
                $filterItemOptions = array_merge(['uri' => $this->getFilterUri($currentUri, $filter, $option)], $itemOptions);

                $menuItem->addChild(
                    $this->getFilterName($filter, $option),
                    $filterItemOptions
                );
            }
        }

        return $menu;
    }

    /**
     * @param array  $itemOptions The options given to the created menuItem
     * @param string $currentUri  The current URI
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createCategoryMenu(array $itemOptions = [], $currentUri = null)
    {
        $menu = $this->factory->createItem('categories', $itemOptions);

        $this->buildCategoryMenu($menu, $itemOptions, $currentUri);

        return $menu;
    }

    /**
     * @param \Knp\Menu\ItemInterface $menu       The item to fill with $routes
     * @param array                   $options    The item options
     * @param string                  $currentUri The current URI
     */
    public function buildCategoryMenu(ItemInterface $menu, array $options = [], $currentUri = null): void
    {
        $categories = $this->categoryManager->getCategoryTree();

        $this->fillMenu($menu, $categories, $options, $currentUri);
    }

    /**
     * Generates the name of the filter based on $filter and $option.
     *
     * @param $filter
     * @param $option
     *
     * @return string
     */
    protected function getFilterName($filter, $option)
    {
        return sprintf('%s_%s', $filter, $option);
    }

    /**
     * Generates the filter uri.
     *
     * @param $currentUri
     * @param $filter
     * @param $option
     *
     * @return string
     */
    protected function getFilterUri($currentUri, $filter, $option)
    {
        return sprintf('%s?filter=%s&option=%s', false !== ($pos = strpos($currentUri, '?')) ? substr($currentUri, 0, $pos) : $currentUri, $filter, $option);
    }

    /**
     * Recursive method to fill $menu with $categories.
     *
     * @param ItemInterface $menu
     * @param array         $categories
     * @param array         $options
     * @param string        $currentUri
     */
    protected function fillMenu(ItemInterface $menu, $categories, array $options = [], $currentUri = null): void
    {
        foreach ($categories as $category) {
            if (false === $category->getEnabled()) {
                continue;
            }

            $fullOptions = array_merge([
                'attributes' => ['class' => ''],      // Ensuring it is set
                'route' => 'sonata_catalog_category',
                'routeParameters' => [
                    'category_id' => $category->getId(),
                    'category_slug' => $category->getSlug(),
                ],
                'extras' => [
                    'safe_label' => true,
                ],
            ], $options);

            if (null === $category->getParent()) {
                $fullOptions['attributes']['class'] = 'lead '.$fullOptions['attributes']['class'];
            }

            $child = $menu->addChild(
                $this->getCategoryTitle($category),
                $fullOptions
            );

            if (count($category->getChildren()) > 0) {
                if (null === $category->getParent()) {
                    $this->fillMenu($menu, $category->getChildren(), $options, $currentUri);
                } else {
                    $this->fillMenu($child, $category->getChildren(), $options, $currentUri);
                }
            }
        }
    }

    /**
     * Gets the HTML associated with the category menu title.
     *
     * @param CategoryInterface $category A category instance
     * @param int               $limit    A limit for calculation (fixed to 500 by default)
     *
     * @return string
     */
    protected function getCategoryTitle(CategoryInterface $category, $limit = 500)
    {
        $count = $this->categoryManager->getProductCount($category, $limit);

        return sprintf('%s <span class="badge pull-right">%d%s</span>', $category->getName(), $count, $count > $limit ? '+' : '');
    }
}
