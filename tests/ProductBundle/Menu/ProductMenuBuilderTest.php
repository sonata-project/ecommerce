<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\ProductBundle\Tests\Menu;

use PHPUnit\Framework\TestCase;
use Sonata\ProductBundle\Menu\ProductMenuBuilder;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class ProductMenuBuilderTest extends TestCase
{
    public function testCreateCategoryMenu()
    {
        $menu = $this->createMock('Knp\Menu\ItemInterface');
        $factory = $this->createMock('Knp\Menu\FactoryInterface');

        $factory->expects($this->once())
            ->method('createItem')
            ->will($this->returnValue($menu));

        $categoryManager = $this->createMock('Sonata\Component\Product\ProductCategoryManagerInterface');
        $router = $this->createMock('Symfony\Component\Routing\RouterInterface');

        $categoryManager->expects($this->once())
            ->method('getCategoryTree')
            ->will($this->returnValue([]));

        $builder = new ProductMenuBuilder($factory, $categoryManager, $router);

        $genMenu = $builder->createCategoryMenu();

        $this->assertInstanceOf('Knp\Menu\ItemInterface', $genMenu);
    }

    public function testCreateFiltersMenu()
    {
        $menu = $this->createMock('Knp\Menu\ItemInterface');
        $factory = $this->createMock('Knp\Menu\FactoryInterface');

        $factory->expects($this->once())
            ->method('createItem')
            ->will($this->returnValue($menu));

        $categoryManager = $this->createMock('Sonata\Component\Product\ProductCategoryManagerInterface');
        $router = $this->createMock('Symfony\Component\Routing\RouterInterface');

        $builder = new ProductMenuBuilder($factory, $categoryManager, $router);

        $productProvider = $this->createMock('Sonata\Component\Product\ProductProviderInterface');

        $productProvider->expects($this->once())
            ->method('getFilters')
            ->will($this->returnValue([]));

        $genMenu = $builder->createFiltersMenu($productProvider);

        $this->assertInstanceOf('Knp\Menu\ItemInterface', $genMenu);
    }
}
