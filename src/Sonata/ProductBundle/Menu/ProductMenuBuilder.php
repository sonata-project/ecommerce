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

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sonata\Component\Product\ProductCategoryManagerInterface;
use Symfony\Component\Routing\RouterInterface;


/**
 * Class ProductMenuBuilder
 *
 * @package Sonata\ProductBundle\Menu
 *
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
     * Constructor
     *
     * @param MenuFactory                     $factory
     * @param ProductCategoryManagerInterface $categoryManager
     * @param RouterInterface                 $router
     */
    public function __construct(FactoryInterface $factory, ProductCategoryManagerInterface $categoryManager, RouterInterface $router)
    {
        $this->factory         = $factory;
        $this->categoryManager = $categoryManager;
        $this->router          = $router;
    }

    /**
     * @param array  $itemOptions The options given to the created menuItem
     * @param string $currentUri  The current URI
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createCategoryMenu(array $itemOptions = array(), $currentUri = null)
    {
        $menu = $this->factory->createItem('categories', $itemOptions);

        $this->buildCategoryMenu($menu, $itemOptions, $currentUri);

        return $menu;
    }

    /**
     * @param \Knp\Menu\ItemInterface $menu        The item to fill with $routes
     * @param array                   $options     The item options
     * @param string                  $currentUri  The current URI
     */
    public function buildCategoryMenu(ItemInterface $menu, array $options = array(), $currentUri = null)
    {
        $categories = $this->categoryManager->getCategoryTree();

        $this->fillMenu($menu, $categories, $options, $currentUri);
    }

    /**
     * Recursive method to fill $menu with $categories
     *
     * @param ItemInterface $menu
     * @param array         $categories
     * @param array         $options
     * @param strubg        $currentUri
     */
    protected function fillMenu(ItemInterface $menu, $categories, array $options = array(), $currentUri = null)
    {
        foreach ($categories as $category) {
            if (null === $category->getParent()) {
                $fullOptions = array_merge(array('attributes' => array('class' => 'nav-header')), $options);
            } else {
                $fullOptions = array_merge(array(
                    'route'           => 'sonata_catalog_category',
                    'routeParameters' => array(
                        'category_id'   => $category->getId(),
                        'category_slug' => $category->getSlug()
                    )
                ), $options);
            }

            $child = $menu->addChild(
                $category->getName(),
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

        $menu->setCurrentUri($currentUri);
    }
}