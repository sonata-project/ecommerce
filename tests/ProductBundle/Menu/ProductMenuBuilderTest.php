<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\Tests\ProductBundle\Menu;

use Sonata\ProductBundle\Menu\ProductMenuBuilder;


/**
 * Class ProductMenuBuilderTest
 *
 * @package Sonata\Tests\ProductBundle\Menu
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class ProductMenuBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateCategoryMenu()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $factory = $this->getMock('Knp\Menu\FactoryInterface');

        $factory->expects($this->once())
            ->method('createItem')
            ->will($this->returnValue($menu));

        $categoryManager = $this->getMock('Sonata\Component\Product\ProductCategoryManagerInterface');
        $router = $this->getMock('Symfony\Component\Routing\RouterInterface');

        $categoryManager->expects($this->once())
            ->method('getCategoryTree')
            ->will($this->returnValue(array()));

        $builder = new ProductMenuBuilder($factory, $categoryManager, $router);

        $genMenu = $builder->createCategoryMenu();

        $this->assertInstanceOf('Knp\Menu\ItemInterface', $genMenu);
    }

    public function testCreateFiltersMenu()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $factory = $this->getMock('Knp\Menu\FactoryInterface');

        $factory->expects($this->once())
            ->method('createItem')
            ->will($this->returnValue($menu));

        $categoryManager = $this->getMock('Sonata\Component\Product\ProductCategoryManagerInterface');
        $router = $this->getMock('Symfony\Component\Routing\RouterInterface');

        $builder = new ProductMenuBuilder($factory, $categoryManager, $router);

        $productProvider = $this->getMock('Sonata\Component\Product\ProductProviderInterface');

        $productProvider->expects($this->once())
            ->method('getFilters')
            ->will($this->returnValue(array()));

        $genMenu = $builder->createFiltersMenu($productProvider);

        $this->assertInstanceOf('Knp\Menu\ItemInterface', $genMenu);
    }
}
