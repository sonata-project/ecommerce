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
     * @return ProductCategoryInterface
     */
    public function createProductCategory();

    /**
     * Deletes a ProductCategory
     *
     * @param  ProductCategoryInterface $productCategory
     * @return void
     */
    public function deleteProductCategory(ProductCategoryInterface $productCategory);

    /**
     * Finds one ProductCategory by the given criteria
     *
     * @param  array                    $criteria
     * @return ProductCategoryInterface
     */
    public function findProductCategoryBy(array $criteria);

    /**
     * Returns the ProductCategory's fully qualified class name
     *
     * @return string
     */
    public function getClass();

    /**
     * Updates a ProductCategory
     *
     * @param  ProductCategoryInterface $productCategory
     * @return void
     */
    public function updateProductCategory(ProductCategoryInterface $productCategory);
}
