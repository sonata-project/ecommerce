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

namespace Sonata\Component\Product;

use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\Doctrine\Model\ManagerInterface;

interface ProductCategoryManagerInterface extends ManagerInterface
{
    /**
     * Adds a Category to a Product.
     *
     * @param ProductInterface  $product  A Product entity
     * @param CategoryInterface $category A Category entity
     * @param bool              $main     Add as the main category?
     */
    public function addCategoryToProduct(ProductInterface $product, CategoryInterface $category, $main = false);

    /**
     * Removes a Category from a Product.
     *
     * @param ProductInterface  $product
     * @param CategoryInterface $category
     */
    public function removeCategoryFromProduct(ProductInterface $product, CategoryInterface $category);

    /**
     * Gets the category tree.
     *
     * @return CategoryInterface[]
     */
    public function getCategoryTree();

    /**
     * Returns the number of products in $category (maxed by $limit).
     *
     * @param CategoryInterface $category
     * @param int               $limit
     *
     * @return int
     */
    public function getProductCount(CategoryInterface $category, $limit = 1000);
}
