<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Component\Product;

use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\CoreBundle\Entity\ManagerInterface;

interface ProductCategoryManagerInterface extends ManagerInterface
{
    /**
     * Adds a Category to a Product.
     *
     * @param ProductInterface  $product
     * @param CategoryInterface $category
     */
    public function addCategoryToProduct(ProductInterface $product, CategoryInterface $category);

    /**
     * Removes a Category from a Product.
     *
     * @param ProductInterface  $product
     * @param CategoryInterface $category
     */
    public function removeCategoryFromProduct(ProductInterface $product, CategoryInterface $category);
}
