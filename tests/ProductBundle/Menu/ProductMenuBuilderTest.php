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

namespace Sonata\ProductBundle\Tests\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use PHPUnit\Framework\TestCase;
use Sonata\Component\Product\ProductCategoryManagerInterface;
use Sonata\Component\Product\ProductProviderInterface;
use Sonata\ProductBundle\Menu\ProductMenuBuilder;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
class ProductMenuBuilderTest extends TestCase
{
    public function testCreateCategoryMenu()
    {
        $menu = $this->createMock(ItemInterface::class);
        $factory = $this->createMock(FactoryInterface::class);

        $factory->expects($this->once())
            ->method('createItem')
            ->will($this->returnValue($menu));

        $categoryManager = $this->createMock(ProductCategoryManagerInterface::class);
        $router = $this->createMock(RouterInterface::class);

        $categoryManager->expects($this->once())
            ->method('getCategoryTree')
            ->will($this->returnValue([]));

        $builder = new ProductMenuBuilder($factory, $categoryManager, $router);

        $genMenu = $builder->createCategoryMenu();

        $this->assertInstanceOf(ItemInterface::class, $genMenu);
    }

    public function testCreateFiltersMenu()
    {
        $menu = $this->createMock(ItemInterface::class);
        $factory = $this->createMock(FactoryInterface::class);

        $factory->expects($this->once())
            ->method('createItem')
            ->will($this->returnValue($menu));

        $categoryManager = $this->createMock(ProductCategoryManagerInterface::class);
        $router = $this->createMock(RouterInterface::class);

        $builder = new ProductMenuBuilder($factory, $categoryManager, $router);

        $productProvider = $this->createMock(ProductProviderInterface::class);

        $productProvider->expects($this->once())
            ->method('getFilters')
            ->will($this->returnValue([]));

        $genMenu = $builder->createFiltersMenu($productProvider);

        $this->assertInstanceOf(ItemInterface::class, $genMenu);
    }
}
