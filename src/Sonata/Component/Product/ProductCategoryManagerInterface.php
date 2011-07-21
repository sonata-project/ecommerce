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

interface ProductCategoryManagerInterface
{
    /**
     * Creates an empty ProductCategory instance
     *
     * @return ProductCategory
     */
    function createProductCategory();

    /**
     * Deletes a ProductCategory
     *
     * @param ProductCategory $productCategory
     * @return void
     */
    function deleteProductCategory(ProductCategoryInterface $productCategory);

    /**
     * Finds one ProductCategory by the given criteria
     *
     * @param array $criteria
     * @return ProductcategoryInterface
     */
    function findProductCategoryBy(array $criteria);

    /**
     * Returns the ProductCategory's fully qualified class name
     *
     * @return string
     */
    function getClass();

    /**
     * Updates a ProductCategory
     *
     * @param ProductCategory $productCategory
     * @return void
     */
    function updateProductCategory(ProductCategoryInterface $productCategory);
}