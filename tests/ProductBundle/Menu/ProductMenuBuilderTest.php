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
    public function testCreateCategoryMenu(): void
    {
        $menu = $this->createMock(ItemInterface::class);
        $factory = $this->createMock(FactoryInterface::class);

        $factory->expects(static::once())
            ->method('createItem')
            ->willReturn($menu);

        $categoryManager = $this->createMock(ProductCategoryManagerInterface::class);
        $router = $this->createMock(RouterInterface::class);

        $categoryManager->expects(static::once())
            ->method('getCategoryTree')
            ->willReturn([]);

        $builder = new ProductMenuBuilder($factory, $categoryManager, $router);

        $genMenu = $builder->createCategoryMenu();

        static::assertInstanceOf(ItemInterface::class, $genMenu);
    }

    public function testCreateFiltersMenu(): void
    {
        $menu = $this->createMock(ItemInterface::class);
        $factory = $this->createMock(FactoryInterface::class);

        $factory->expects(static::once())
            ->method('createItem')
            ->willReturn($menu);

        $categoryManager = $this->createMock(ProductCategoryManagerInterface::class);
        $router = $this->createMock(RouterInterface::class);

        $builder = new ProductMenuBuilder($factory, $categoryManager, $router);

        $productProvider = $this->createMock(ProductProviderInterface::class);

        $productProvider->expects(static::once())
            ->method('getFilters')
            ->willReturn([]);

        $genMenu = $builder->createFiltersMenu($productProvider);

        static::assertInstanceOf(ItemInterface::class, $genMenu);
    }
}
